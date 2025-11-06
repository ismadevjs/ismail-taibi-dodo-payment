# Installation Guide

## Quick Install

```bash
composer require dodopayments/laravel-dodopayments
```

That's it! The package will auto-register.

## Step-by-Step Setup

### 1. Install Package

```bash
composer require dodopayments/laravel-dodopayments
```

### 2. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=dodopayments-config
```

### 3. Configure Environment Variables

Add to your `.env` file:

```env
# Required
DODO_PAYMENTS_API_KEY=your_api_key_here
DODO_PAYMENTS_PUBLISHABLE_KEY=your_publishable_key_here
DODO_PAYMENTS_WEBHOOK_SECRET=your_webhook_secret_here
DODO_PAYMENTS_ENVIRONMENT=test_mode

# Optional - Customize URLs
DODO_PAYMENTS_SUCCESS_URL=/payment/success
DODO_PAYMENTS_CANCEL_URL=/payment/cancel

# Optional - For quick testing
DODO_PRODUCT_ID=prod_your_product_id
```

### 4. Get Your Credentials

#### API Key
1. Go to [DodoPayments Dashboard](https://app.dodopayments.com)
2. Navigate to **Developer → API**
3. Click **Generate API Key**
4. Copy the key

#### Webhook Secret
1. Go to **Developer → Webhooks**
2. Click **Create Webhook**
3. Add URL: `https://your-domain.com/webhook/dodopayments`
4. Select events (or select all)
5. Copy the webhook secret

#### Product ID
1. Go to **Products**
2. Create or select a product
3. Copy the Product ID (starts with `prod_`)

### 5. Test Installation

```bash
# Clear cache
php artisan config:clear

# Start server
php artisan serve

# Visit checkout
open http://localhost:8000/payment/checkout
```

## Customization

### Publish Views

If you want to customize the checkout pages:

```bash
php artisan vendor:publish --tag=dodopayments-views
```

Views will be in `resources/views/vendor/dodopayments/`

### Disable Auto Routes

If you want to register routes manually:

```php
// config/dodopayments.php
'routes' => [
    'enabled' => false,
],
```

### Custom Route Prefix

```php
// config/dodopayments.php
'routes' => [
    'prefix' => 'payments', // /payment/* becomes /payments/*
],
```

## Webhook Setup

### Production

1. In DodoPayments dashboard: **Developer → Webhooks**
2. Add URL: `https://yourdomain.com/webhook/dodopayments`
3. Select events
4. Copy webhook secret
5. Add to `.env`: `DODO_PAYMENTS_WEBHOOK_SECRET=your_secret`

### Development (Local Testing)

Use ngrok to test webhooks locally:

```bash
# Start ngrok
ngrok http 8000

# Use the ngrok URL in webhook settings
# Example: https://abc123.ngrok.io/webhook/dodopayments
```

## Event Listener Setup

Create a listener for payment events:

```bash
php artisan make:listener HandlePaymentSuccess
```

Edit `app/Listeners/HandlePaymentSuccess.php`:

```php
<?php

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
                'payment_id' => $data['payment_id'],
            ]);
            
            // Your business logic here
        }
    }
}
```

Register in `app/Providers/EventServiceProvider.php`:

```php
use DodoPayments\Laravel\Events\WebhookReceived;

protected $listen = [
    WebhookReceived::class => [
        \App\Listeners\HandlePaymentSuccess::class,
    ],
];
```

## Verification

Check routes are registered:

```bash
php artisan route:list | grep dodopayments
```

Expected output:
```
GET|HEAD   payment/checkout ............ dodopayments.checkout
POST       payment/checkout ............ dodopayments.create-checkout
GET|HEAD   payment/success ............. dodopayments.success
GET|HEAD   payment/cancel .............. dodopayments.cancel
POST       payment/create-link ......... dodopayments.create-link
POST       webhook/dodopayments ........ dodopayments.webhook
```

## Troubleshooting

### Routes Not Found

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Views Not Loading

```bash
php artisan view:clear
```

### Config Not Updated

```bash
php artisan config:clear
```

### Webhook Signature Fails

- Verify webhook secret is correct
- Check you're using raw request body
- Ensure timestamp is within 5 minutes

## Going Live

1. Get production API keys from dashboard
2. Update `.env`:
   ```env
   DODO_PAYMENTS_ENVIRONMENT=live_mode
   DODO_PAYMENTS_API_KEY=your_live_api_key
   ```
3. Update webhook URL to production domain
4. Test with real (small amount) transaction
5. Monitor logs and webhook events

## Support

- 📧 [email protected]
- 📚 [Documentation](https://docs.dodopayments.com)
- 🐛 [GitHub Issues](https://github.com/dodopayments/laravel-dodopayments/issues)
