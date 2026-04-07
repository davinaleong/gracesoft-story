<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostmarkWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $configuredToken = (string) config('services.postmark.webhook_token', '');
        $receivedToken = (string) $request->header('X-Postmark-Server-Token', '');

        if ($configuredToken === '' || ! hash_equals($configuredToken, $receivedToken)) {
            return response()->json(['message' => 'Invalid token.'], 401);
        }

        /** @var array<string, mixed> $payload */
        $payload = $request->json()->all();

        Log::info('Postmark webhook received.', [
            'record_type' => $payload['RecordType'] ?? null,
            'message_id' => $payload['MessageID'] ?? null,
        ]);

        return response()->json(['message' => 'Webhook processed.'], 200);
    }
}
