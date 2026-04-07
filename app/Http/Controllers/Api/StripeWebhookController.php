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
}
