<?php

namespace DodoPayments\Laravel\Http\Controllers;

use DodoPayments\Laravel\Facades\DodoPayments;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle DodoPayments webhook
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        
        $headers = [
            'webhook-id' => $request->header('webhook-id'),
            'webhook-signature' => $request->header('webhook-signature'),
            'webhook-timestamp' => $request->header('webhook-timestamp'),
        ];

        if (!DodoPayments::verifyWebhookSignature($payload, $headers)) {
            Log::warning('DodoPayments webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = json_decode($payload, true);

        if (!$event) {
            Log::error('DodoPayments webhook: Invalid JSON payload');
            return response()->json(['error' => 'Invalid JSON'], 400);
        }

        Log::info('DodoPayments webhook received', [
            'event_type' => $event['type'] ?? 'unknown',
            'event_id' => $event['id'] ?? 'unknown',
        ]);

        try {
            $eventType = $event['type'] ?? null;

            // Fire Laravel event for the webhook
            event(new \DodoPayments\Laravel\Events\WebhookReceived($eventType, $event));

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('DodoPayments webhook processing error: ' . $e->getMessage(), [
                'event' => $event,
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }
}
