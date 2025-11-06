# Laravel DodoPayments

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dodopayments/laravel-dodopayments.svg?style=flat-square)](https://packagist.org/packages/dodopayments/laravel-dodopayments)
[![Total Downloads](https://img.shields.io/packagist/dt/dodopayments/laravel-dodopayments.svg?style=flat-square)](https://packagist.org/packages/dodopayments/laravel-dodopayments)

Official Laravel package for DodoPayments - Accept payments with ease in your Laravel applications.

## Features

- 🚀 **Easy Integration** - Get started in minutes
- 💳 **Checkout Sessions** - Secure hosted checkout pages
- 🔗 **Payment Links** - Static and dynamic payment URLs
- 🔔 **Webhook Support** - Real-time payment notifications
- 🎨 **Beautiful UI** - Pre-built, customizable checkout views
- 🔐 **Secure** - Built-in webhook signature verification
- 📦 **Laravel Standard** - Follows Laravel best practices
- 🎯 **Event-Driven** - Laravel events for all webhooks

## Requirements

- PHP 8.0 or higher
- Laravel 9.x, 10.x, or 11.x
- A DodoPayments account ([Sign up here](https://app.dodopayments.com))

## Installation

Install the package via Composer:

```bash
composer require odopayments-ismail-taibi/laravel-dodopayments
```

### Publish Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=dodopayments-config
```

### Publish Views (Optional)

If you want to customize the checkout views:

```bash
php artisan vendor:publish --tag=dodopayments-views
```

## Configuration

Add your DodoPayments credentials to your `.env` file:

```env
DODO_PAYMENTS_API_KEY=your_api_key_here
DODO_PAYMENTS_PUBLISHABLE_KEY=your_publishable_key_here
DODO_PAYMENTS_WEBHOOK_SECRET=your_webhook_secret_here
DODO_PAYMENTS_ENVIRONMENT=test_mode

# Optional: Customize URLs
DODO_PAYMENTS_SUCCESS_URL=/payment/success
DODO_PAYMENTS_CANCEL_URL=/payment/cancel

# Optional: Product ID for quick testing
DODO_PRODUCT_ID=prod_your_product_id
```

### Getting Your Credentials

1. **API Key**: Dashboard → Developer → API
2. **Webhook Secret**: Dashboard → Developer → Webhooks
3. **Product ID**: Dashboard → Products → Select Product

## Quick Start

### 1. Basic Checkout

The package automatically registers routes. Visit:

```
http://your-app.test/payment/checkout
```

### 2. Create Checkout Programmatically

```php
use DodoPayments\Laravel\Facades\DodoPayments;

$session = DodoPayments::createCheckoutSession(
    productCart: [
        [
            'product_id' => 'prod_abc123',
            'quantity' => 1,
        ]
    ],
    options: [
        'customer' => [
            'email' => '[email protected]',
            'name' => 'John Doe',
        ],
        'return_url' => url('/payment/success'),
        'metadata' => [
            'order_id' => 'order_123',
        ],
    ]
);

return redirect()->away($session['checkout_url']);
```

### 3. Handle Webhooks

The package fires Laravel events for all webhooks. Listen to them in your `EventServiceProvider`:

```php
use DodoPayments\Laravel\Events\WebhookReceived;

protected $listen = [
    WebhookReceived::class => [
        \App\Listeners\HandlePaymentSuccess::class,
    ],
];
```

Create a listener:

```php
namespace App\Listeners;

use DodoPayments\Laravel\Events\WebhookReceived;
use Illuminate\Support\Facades\Log;

class HandlePaymentSuccess
{
    public function handle(WebhookReceived $event): void
    {
        if ($event->type === 'payment.succeeded') {
            $data = $event->data['data'] ?? [];
            
            Log::info('Payment successful', [
                'payment_id' => $data['payment_id'] ?? null,
                'amount' => $data['amount'] ?? null,
            ]);
            
            // Your business logic here
            // Update order status, send emails, etc.
        }
    }
}
```

## Usage

### Create Checkout Session

```php
use DodoPayments\Laravel\Facades\DodoPayments;

// Simple checkout
$session = DodoPayments::createCheckoutSession([
    ['product_id' => 'prod_abc123', 'quantity' => 1]
]);

// With customer details
$session = DodoPayments::createCheckoutSession(
    productCart: [
        ['product_id' => 'prod_abc123', 'quantity' => 2],
        ['product_id' => 'prod_xyz789', 'quantity' => 1],
    ],
    options: [
        'customer' => [
            'email' => '[email protected]',
            'name' => 'Jane Smith',
        ],
        'billing_address' => [
            'street' => '123 Main St',
            'city' => 'San Francisco',
            'state' => 'CA',
            'country' => 'US',
            'zipcode' => '94103',
        ],
        'metadata' => [
            'user_id' => auth()->id(),
            'order_id' => 'order_456',
        ],
        'return_url' => url('/orders/success'),
    ]
);

redirect()->away($session['checkout_url']);
```

### Create Payment Link

```php
// Static payment link
$link = DodoPayments::buildStaticPaymentLink(
    productId: 'prod_abc123',
    params: [
        'quantity' => 1,
        'email' => '[email protected]',
        'redirect_url' => url('/success'),
    ]
);

// Dynamic payment link
$payment = DodoPayments::createPaymentLink(
    productId: 'prod_abc123',
    options: [
        'billing' => [
            'city' => 'New York',
            'country' => 'US',
            'state' => 'NY',
            'street' => '456 Broadway',
            'zipcode' => 10013,
        ],
        'customer' => [
            'email' => '[email protected]',
            'name' => 'Bob Wilson',
        ],
    ]
);

return $payment['payment_link'];
```

### Retrieve Payment

```php
$payment = DodoPayments::getPayment('pay_123abc');

echo $payment['status']; // succeeded, failed, pending, etc.
echo $payment['amount'];
echo $payment['customer']['email'];
```

### List Payments

```php
$payments = DodoPayments::listPayments([
    'limit' => 10,
    'page' => 1,
]);

foreach ($payments['data'] as $payment) {
    echo $payment['payment_id'];
}
```

## Webhook Events

The package automatically verifies webhook signatures and fires the `WebhookReceived` event.

### Available Event Types

- `payment.succeeded` - Payment completed successfully
- `payment.failed` - Payment failed
- `payment.refunded` - Payment was refunded
- `subscription.created` - New subscription created
- `subscription.updated` - Subscription details updated
- `subscription.cancelled` - Subscription cancelled
- `subscription.renewed` - Subscription renewed

### Webhook Configuration

1. In your DodoPayments dashboard, go to Developer → Webhooks
2. Add webhook URL: `https://your-domain.com/webhook/dodopayments`
3. Copy the webhook secret
4. Add to `.env`: `DODO_PAYMENTS_WEBHOOK_SECRET=your_secret`

### Example Webhook Listener

```php
namespace App\Listeners;

use DodoPayments\Laravel\Events\WebhookReceived;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class HandlePaymentSuccess
{
    public function handle(WebhookReceived $event): void
    {
        match($event->type) {
            'payment.succeeded' => $this->handlePaymentSuccess($event->data),
            'payment.refunded' => $this->handleRefund($event->data),
            'subscription.created' => $this->handleSubscriptionCreated($event->data),
            default => null,
        };
    }
    
    private function handlePaymentSuccess(array $data): void
    {
        $paymentData = $data['data'] ?? [];
        $orderId = $paymentData['metadata']['order_id'] ?? null;
        
        if ($orderId) {
            Order::where('id', $orderId)->update([
                'status' => 'paid',
                'payment_id' => $paymentData['payment_id'],
                'paid_at' => now(),
            ]);
            
            // Send confirmation email
            Mail::to($paymentData['customer']['email'])
                ->send(new OrderConfirmation($orderId));
        }
    }
}
```

## Routes

The package automatically registers these routes:

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/payment/checkout` | `dodopayments.checkout` | Checkout page |
| POST | `/payment/checkout` | `dodopayments.create-checkout` | Create checkout session |
| GET | `/payment/success` | `dodopayments.success` | Success page |
| GET | `/payment/cancel` | `dodopayments.cancel` | Cancel page |
| POST | `/payment/create-link` | `dodopayments.create-link` | Create payment link |
| POST | `/webhook/dodopayments` | `dodopayments.webhook` | Webhook endpoint |

### Disable Auto Routes

If you want to register routes manually, disable them in config:

```php
// config/dodopayments.php
'routes' => [
    'enabled' => false,
],
```

## Customization

### Custom Views

Publish and customize the views:

```bash
php artisan vendor:publish --tag=dodopayments-views
```

Views will be available in `resources/views/vendor/dodopayments/`:

- `checkout.blade.php` - Checkout page
- `success.blade.php` - Success page
- `cancel.blade.php` - Cancel page

### Custom Route Prefix

```php
// config/dodopayments.php
'routes' => [
    'prefix' => 'payments', // Changes /payment/* to /payments/*
],
```

## Testing

The package uses the test mode API by default when `DODO_PAYMENTS_ENVIRONMENT=test_mode`.

### Test Locally

```bash
php artisan serve

# Visit: http://localhost:8000/payment/checkout
```

### Test Webhooks with ngrok

```bash
ngrok http 8000

# Use the ngrok URL in your DodoPayments webhook settings
# Example: https://abc123.ngrok.io/webhook/dodopayments
```

## API Reference

### Facade Methods

```php
// Create checkout session
DodoPayments::createCheckoutSession(array $productCart, array $options = []): array

// Create payment link
DodoPayments::createPaymentLink(string $productId, array $options = []): array

// Get payment details
DodoPayments::getPayment(string $paymentId): array

// List payments
DodoPayments::listPayments(array $filters = []): array

// Build static payment link
DodoPayments::buildStaticPaymentLink(string $productId, array $params = []): string

// Verify webhook signature
DodoPayments::verifyWebhookSignature(string $payload, array $headers): bool

// Get API URL
DodoPayments::getApiUrl(): string

// Get environment
DodoPayments::getEnvironment(): string
```

## Security

- Webhook signatures are automatically verified
- API keys should be stored in `.env` file
- Never commit API keys to version control
- Use HTTPS in production

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Support

- 📧 Email: [email protected]
- 📚 Documentation: [https://docs.dodopayments.com](https://docs.dodopayments.com)
- 🐛 Issues: [GitHub Issues](https://github.com/dodopayments/laravel-dodopayments/issues)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

Made with ❤️ by [DodoPayments](https://dodopayments.com)
