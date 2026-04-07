<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Plan;
use App\Models\StripeWebhookEvent;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = (string) $request->header('Stripe-Signature', '');
        $secret = (string) config('services.stripe.webhook_secret', '');

        if (! $this->isValidSignature($payload, $signature, $secret)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        /** @var array<string, mixed> $event */
        $event = json_decode($payload, true) ?? [];
        $eventId = (string) ($event['id'] ?? '');

        if ($eventId === '') {
            return response()->json(['message' => 'Invalid event payload.'], 422);
        }

        if (StripeWebhookEvent::query()->where('event_id', $eventId)->exists()) {
            return response()->json(['message' => 'Already processed.'], 200);
        }

        StripeWebhookEvent::query()->create([
            'event_id' => $eventId,
            'event_type' => (string) ($event['type'] ?? 'unknown'),
            'processed_at' => now(),
        ]);

        $this->applyCatalogEvent($event);
        $this->applySubscriptionEvent($event);

        return response()->json(['message' => 'Webhook processed.'], 200);
    }

    private function isValidSignature(string $payload, string $signatureHeader, string $secret): bool
    {
        if ($secret === '' || $signatureHeader === '') {
            return false;
        }

        $parts = [];

        foreach (explode(',', $signatureHeader) as $item) {
            [$key, $value] = array_pad(explode('=', trim($item), 2), 2, null);

            if ($key !== null && $value !== null) {
                $parts[$key] = $value;
            }
        }

        $timestamp = $parts['t'] ?? null;
        $signature = $parts['v1'] ?? null;

        if ($timestamp === null || $signature === null) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        return hash_equals($expected, $signature);
    }

    /**
     * @param array<string, mixed> $event
     */
    private function applySubscriptionEvent(array $event): void
    {
        $type = (string) ($event['type'] ?? '');
        if (! in_array($type, ['customer.subscription.created', 'customer.subscription.updated', 'customer.subscription.deleted'], true)) {
            return;
        }

        /** @var array<string, mixed> $object */
        $object = is_array($event['data']['object'] ?? null) ? $event['data']['object'] : [];
        $customerId = (string) ($object['customer'] ?? '');
        $stripeSubscriptionId = (string) ($object['id'] ?? '');

        if ($customerId === '' || $stripeSubscriptionId === '') {
            return;
        }

        $account = Account::query()->where('stripe_customer_id', $customerId)->first();
        if (! $account) {
            Log::warning('Stripe webhook skipped: account not found for customer.', [
                'customer_id' => $customerId,
                'subscription_id' => $stripeSubscriptionId,
            ]);

            return;
        }

        $plan = $this->resolvePlan($object);

        if (! $plan) {
            Log::warning('Stripe webhook skipped: plan could not be resolved.', [
                'customer_id' => $customerId,
                'subscription_id' => $stripeSubscriptionId,
                'price_id' => $this->extractPriceId($object),
                'tier' => $this->extractTier($object),
            ]);

            return;
        }

        $status = (string) ($object['status'] ?? 'active');
        $currentPeriodEnd = isset($object['current_period_end'])
            ? now()->setTimestamp((int) $object['current_period_end'])
            : null;

        $subscription = Subscription::query()->firstOrNew([
            'stripe_subscription_id' => $stripeSubscriptionId,
        ]);

        if (! $subscription->exists) {
            $subscription->id = (string) Str::uuid();
        }

        $subscription->account_id = $account->id;
        $subscription->plan_id = $plan->id;
        $subscription->status = $type === 'customer.subscription.deleted' ? 'canceled' : $status;
        $subscription->current_period_end = $currentPeriodEnd;
        $subscription->save();
    }

    /**
     * @param array<string, mixed> $event
     */
    private function applyCatalogEvent(array $event): void
    {
        $type = (string) ($event['type'] ?? '');
        if (! in_array($type, ['product.created', 'product.updated', 'product.deleted', 'price.created', 'price.updated', 'price.deleted'], true)) {
            return;
        }

        /** @var array<string, mixed> $object */
        $object = is_array($event['data']['object'] ?? null) ? $event['data']['object'] : [];

        if (str_starts_with($type, 'product.')) {
            $this->syncProductCatalog($object, $type);

            return;
        }

        $this->syncPriceCatalog($object, $type);
    }

    /**
     * @param array<string, mixed> $product
     */
    private function syncProductCatalog(array $product, string $eventType): void
    {
        $productId = (string) ($product['id'] ?? '');
        if ($productId === '') {
            return;
        }

        $plan = Plan::query()->where('stripe_product_id', $productId)->first();

        $tier = $this->extractTier($product);
        if (! $plan && $tier !== '') {
            $plan = Plan::query()->firstOrNew(['slug' => $tier]);
        }

        if (! $plan) {
            $name = trim((string) ($product['name'] ?? ''));
            $baseSlug = strtolower(trim((string) str($name)->slug('-')));
            $slug = $baseSlug !== '' ? $baseSlug : 'plan-'.strtolower(substr(str_replace('-', '', (string) Str::uuid()), 0, 8));

            $plan = Plan::query()->firstOrNew(['slug' => $slug]);
            $tier = $tier !== '' ? $tier : $slug;
        }

        if (! $plan->exists) {
            $plan->id = (string) Str::uuid();
        }

        if ($tier !== '') {
            $this->applyTierDefaults($plan, $tier);
        }

        $plan->name = (string) ($product['name'] ?? ucfirst($tier));
        $plan->stripe_product_details = $this->extractProductDetails($product);

        if ($eventType === 'product.deleted') {
            if ($plan->stripe_product_id === $productId) {
                $plan->stripe_product_id = null;
                $plan->save();
            }

            return;
        }

        $plan->stripe_product_id = $productId;
        $plan->save();
    }

    /**
     * @param array<string, mixed> $price
     */
    private function syncPriceCatalog(array $price, string $eventType): void
    {
        $priceId = (string) ($price['id'] ?? '');
        if ($priceId === '') {
            return;
        }

        $productId = is_string($price['product'] ?? null) ? (string) $price['product'] : '';

        $plan = null;
        $tier = $this->extractTier($price);
        if ($tier !== '') {
            $plan = Plan::query()->firstOrNew(['slug' => $tier]);
            if (! $plan->exists) {
                $plan->id = (string) Str::uuid();
                $plan->name = ucfirst($tier);
            }

            $this->applyTierDefaults($plan, $tier);
        } elseif ($productId !== '') {
            $plan = Plan::query()->where('stripe_product_id', $productId)->first();
        }

        if (! $plan) {
            Log::warning('Stripe price webhook skipped: plan could not be resolved.', [
                'price_id' => $priceId,
                'product_id' => $productId,
            ]);

            return;
        }

        if ($eventType === 'price.deleted') {
            if ($plan->stripe_price_id === $priceId) {
                $plan->stripe_price_id = null;
                $plan->save();
            }

            return;
        }

        // Enforce unique mapping: one Stripe price id to one plan.
        Plan::query()
            ->where('stripe_price_id', $priceId)
            ->where('id', '!=', $plan->id)
            ->update(['stripe_price_id' => null]);

        if ($productId !== '') {
            $plan->stripe_product_id = $productId;
        }

        $plan->stripe_price_id = $priceId;
        $plan->save();
    }

    /**
     * @param array<string, mixed> $object
     */
    private function resolvePlan(array $object): ?Plan
    {
        $priceId = $this->extractPriceId($object);
        if ($priceId !== '') {
            $plan = Plan::query()->where('stripe_price_id', $priceId)->first();
            if ($plan) {
                return $plan;
            }
        }

        $tier = $this->extractTier($object);
        if ($tier !== '') {
            $plan = Plan::query()->where('slug', $tier)->first();
            if ($plan) {
                return $plan;
            }
        }

        return Plan::query()->where('slug', 'free')->first();
    }

    /**
     * @param array<string, mixed> $object
     */
    private function extractPriceId(array $object): string
    {
        $priceId = $object['items']['data'][0]['price']['id'] ?? null;

        return is_string($priceId) ? $priceId : '';
    }

    /**
     * @param array<string, mixed> $object
     */
    private function extractTier(array $object): string
    {
        $app = $object['metadata']['app']
            ?? $object['items']['data'][0]['price']['metadata']['app']
            ?? null;

        if (is_string($app) && strtolower(trim($app)) !== 'story') {
            return '';
        }

        $tier = $object['metadata']['tier']
            ?? $object['items']['data'][0]['price']['metadata']['tier']
            ?? null;

        if (! is_string($tier)) {
            return '';
        }

        $normalized = strtolower(trim($tier));

        return in_array($normalized, ['free', 'growth', 'pro'], true)
            ? $normalized
            : '';
    }

    /**
     * @param array<string, mixed> $product
     * @return array<string, mixed>
     */
    private function extractProductDetails(array $product): array
    {
        return [
            'name' => is_string($product['name'] ?? null) ? $product['name'] : null,
            'description' => is_string($product['description'] ?? null) ? $product['description'] : null,
            'active' => (bool) ($product['active'] ?? false),
            'metadata' => is_array($product['metadata'] ?? null) ? $product['metadata'] : [],
        ];
    }

    private function applyTierDefaults(Plan $plan, string $tier): void
    {
        $defaults = match ($tier) {
            'free' => [
                'max_users' => 1,
                'max_timelines' => 1,
                'storage_mb' => 100,
                'max_items' => 50,
                'max_replies' => 100,
                'can_use_integrations' => false,
                'can_collaborate' => false,
                'can_use_auto_sync' => false,
                'can_use_smart_automation' => false,
                'can_use_activity_logs' => false,
                'can_use_priority_sync' => false,
                'can_use_advanced_privacy' => false,
                'can_share_private_links' => false,
                'can_use_insights' => false,
            ],
            'growth' => [
                'max_users' => 5,
                'max_timelines' => 10,
                'storage_mb' => 5120,
                'max_items' => 500,
                'max_replies' => 2000,
                'can_use_integrations' => true,
                'can_collaborate' => false,
                'can_use_auto_sync' => false,
                'can_use_smart_automation' => false,
                'can_use_activity_logs' => false,
                'can_use_priority_sync' => false,
                'can_use_advanced_privacy' => false,
                'can_share_private_links' => true,
                'can_use_insights' => false,
            ],
            'pro' => [
                'max_users' => 20,
                'max_timelines' => null,
                'storage_mb' => 51200,
                'max_items' => 5000,
                'max_replies' => 20000,
                'can_use_integrations' => true,
                'can_collaborate' => true,
                'can_use_auto_sync' => true,
                'can_use_smart_automation' => true,
                'can_use_activity_logs' => true,
                'can_use_priority_sync' => true,
                'can_use_advanced_privacy' => true,
                'can_share_private_links' => true,
                'can_use_insights' => true,
            ],
            default => [
                'max_users' => 1,
                'max_timelines' => null,
                'storage_mb' => null,
                'max_items' => null,
                'max_replies' => null,
                'can_use_integrations' => false,
                'can_collaborate' => false,
                'can_use_auto_sync' => false,
                'can_use_smart_automation' => false,
                'can_use_activity_logs' => false,
                'can_use_priority_sync' => false,
                'can_use_advanced_privacy' => false,
                'can_share_private_links' => false,
                'can_use_insights' => false,
            ],
        };

        foreach ($defaults as $key => $value) {
            $plan->{$key} = $value;
        }
    }
}
