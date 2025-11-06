<?php

namespace DodoPayments\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array createCheckoutSession(array $productCart, array $options = [])
 * @method static array createPaymentLink(string $productId, array $options = [])
 * @method static array getPayment(string $paymentId)
 * @method static array listPayments(array $filters = [])
 * @method static bool verifyWebhookSignature(string $payload, array $headers)
 * @method static string buildStaticPaymentLink(string $productId, array $params = [])
 * @method static string getApiUrl()
 * @method static string getEnvironment()
 *
 * @see \DodoPayments\Laravel\DodoPayments
 */
class DodoPayments extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'dodopayments';
    }
}
