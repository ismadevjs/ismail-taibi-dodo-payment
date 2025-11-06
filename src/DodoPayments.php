<?php

namespace DodoPayments\Laravel;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DodoPayments
{
    protected $apiKey;
    protected $apiUrl;
    protected $environment;

    public function __construct()
    {
        $this->apiKey = config('dodopayments.api_key');
        $this->environment = config('dodopayments.environment', 'test_mode');
        
        // Use correct API URL based on environment
        $this->apiUrl = $this->environment === 'test_mode'
            ? 'https://test.dodopayments.com'
            : 'https://api.dodopayments.com';
    }

    /**
     * Create a checkout session
     *
     * @param array $productCart Array of products with product_id and quantity
     * @param array $options Optional parameters (customer, billing, metadata, etc.)
     * @return array
     * @throws Exception
     */
    public function createCheckoutSession(array $productCart, array $options = []): array
    {
        // Ensure quantities are integers
        foreach ($productCart as &$item) {
            if (isset($item['quantity'])) {
                $item['quantity'] = (int) $item['quantity'];
            }
        }

        $payload = ['product_cart' => $productCart];

        // Add optional parameters
        if (isset($options['customer'])) {
            $payload['customer'] = $options['customer'];
        }

        if (isset($options['billing_address'])) {
            $payload['billing_address'] = $options['billing_address'];
        }

        if (isset($options['metadata'])) {
            $payload['metadata'] = $options['metadata'];
        }

        if (isset($options['return_url'])) {
            $payload['return_url'] = $options['return_url'];
        }

        if (isset($options['allowed_payment_method_types'])) {
            $payload['allowed_payment_method_types'] = $options['allowed_payment_method_types'];
        }

        if (isset($options['confirm'])) {
            $payload['confirm'] = $options['confirm'];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/checkouts', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            $errorDetails = $response->json();
            Log::error('DodoPayments API Error Response', [
                'status' => $response->status(),
                'body' => $errorDetails,
            ]);

            throw new Exception('DodoPayments API Error: ' . ($errorDetails['message'] ?? $response->body()));

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('DodoPayments Connection Error', [
                'message' => $e->getMessage(),
                'api_url' => $this->apiUrl,
            ]);
            throw new Exception('Unable to connect to DodoPayments. Please check your internet connection.');
        }
    }

    /**
     * Create a one-time payment link
     *
     * @param string $productId
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function createPaymentLink(string $productId, array $options = []): array
    {
        $payload = [
            'product_id' => $productId,
            'payment_link' => true,
        ];

        $payload = array_merge($payload, $options);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/payments', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('DodoPayments API Error: ' . $response->body());
        } catch (Exception $e) {
            Log::error('DodoPayments Payment Link Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve a payment by ID
     *
     * @param string $paymentId
     * @return array
     * @throws Exception
     */
    public function getPayment(string $paymentId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiUrl . '/payments/' . $paymentId);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('DodoPayments API Error: ' . $response->body());
        } catch (Exception $e) {
            Log::error('DodoPayments Get Payment Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * List payments
     *
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public function listPayments(array $filters = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiUrl . '/payments', $filters);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('DodoPayments API Error: ' . $response->body());
        } catch (Exception $e) {
            Log::error('DodoPayments List Payments Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify webhook signature using Standard Webhooks specification
     *
     * @param string $payload The raw request body
     * @param array $headers Webhook headers (webhook-id, webhook-signature, webhook-timestamp)
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, array $headers): bool
    {
        $webhookSecret = config('dodopayments.webhook_secret');
        
        if (empty($webhookSecret)) {
            Log::warning('DodoPayments webhook secret is not configured');
            return false;
        }

        $signature = $headers['webhook-signature'] ?? $headers['webhook_signature'] ?? '';
        
        if (empty($signature)) {
            return false;
        }

        $parts = explode(',', $signature);
        
        if (count($parts) !== 3) {
            return false;
        }

        [$version, $timestamp, $sig] = $parts;

        // Check timestamp to prevent replay attacks (within 5 minutes)
        $currentTime = time();
        if (abs($currentTime - intval($timestamp)) > 300) {
            Log::warning('DodoPayments webhook timestamp is too old');
            return false;
        }

        // Calculate expected signature
        $signedPayload = $timestamp . '.' . $payload;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $webhookSecret);

        return hash_equals($expectedSignature, $sig);
    }

    /**
     * Build static payment link with query parameters
     *
     * @param string $productId
     * @param array $params
     * @return string
     */
    public function buildStaticPaymentLink(string $productId, array $params = []): string
    {
        $baseUrl = 'https://checkout.dodopayments.com/buy/' . $productId;
        
        if (!empty($params)) {
            $baseUrl .= '?' . http_build_query($params);
        }

        return $baseUrl;
    }

    /**
     * Get the API URL based on environment
     *
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * Get the current environment
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
