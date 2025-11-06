<?php

namespace DodoPayments\Laravel\Http\Controllers;

use DodoPayments\Laravel\Facades\DodoPayments;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{
    /**
     * Show checkout page
     */
    public function showCheckout()
    {
        return view('dodopayments::checkout');
    }

    /**
     * Create a checkout session and redirect to DodoPayments
     */
    public function createCheckout(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'customer_email' => 'nullable|email',
                'customer_name' => 'nullable|string',
            ]);

            $productCart = [
                [
                    'product_id' => $validated['product_id'],
                    'quantity' => (int) $validated['quantity'],
                ]
            ];

            $options = [
                'return_url' => url(config('dodopayments.urls.success') . '?session_id={CHECKOUT_SESSION_ID}'),
                'metadata' => [
                    'user_id' => auth()->id() ?? 'guest',
                    'order_id' => uniqid('order_'),
                ],
            ];

            if (!empty($validated['customer_email']) || !empty($validated['customer_name'])) {
                $options['customer'] = array_filter([
                    'email' => $validated['customer_email'] ?? null,
                    'name' => $validated['customer_name'] ?? null,
                ]);
            }

            $session = DodoPayments::createCheckoutSession($productCart, $options);

            return redirect()->away($session['checkout_url']);

        } catch (Exception $e) {
            Log::error('Checkout creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create checkout session. Please try again.');
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $paymentId = $request->query('payment_id');

        return view('dodopayments::success', [
            'session_id' => $sessionId,
            'payment_id' => $paymentId,
        ]);
    }

    /**
     * Handle cancelled payment
     */
    public function cancel()
    {
        return view('dodopayments::cancel');
    }

    /**
     * Create a payment link (API endpoint)
     */
    public function createPaymentLink(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|string',
                'quantity' => 'required|integer|min:1',
            ]);

            $paymentLink = DodoPayments::buildStaticPaymentLink(
                $validated['product_id'],
                [
                    'quantity' => $validated['quantity'],
                    'redirect_url' => url(config('dodopayments.urls.success')),
                ]
            );

            return response()->json([
                'success' => true,
                'payment_link' => $paymentLink,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
