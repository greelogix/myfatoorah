# MyFatoorah Laravel Package

A lightweight Laravel package for MyFatoorah payment gateway integration with support for payment initiation, payment methods retrieval, webhooks, and recurring payments.

## Features

- ✅ Complete MyFatoorah API integration
- ✅ Get payment methods from API
- ✅ Initiate and execute payments
- ✅ Webhook handling with signature validation
- ✅ Recurring payments support
- ✅ Automatic mobile number formatting
- ✅ Country and language support
- ✅ Laravel HTTP Client (no external dependencies)
- ✅ Laravel 10.x and 11.x compatible
- ✅ Auto-discovery enabled
- ✅ Comprehensive error handling
- ✅ Payment status tracking

## Requirements

- PHP >= 8.1
- Laravel 10.x or 11.x
- Composer
- MyFatoorah API Key

## Installation

### Step 1: Install via Composer

#### Option A: Install from Git Repository (Private/Public)

1. **Add repository to your Laravel project's `composer.json`:**

   ```json
   {
       "repositories": [
           {
               "type": "vcs",
               "url": "https://github.com/greelogix/myfatoorah.git"
           }
       ],
       "require": {
           "greelogix/myfatoorah-laravel": "dev-main"
       }
   }
   ```

2. **If using private repository, configure authentication:**

   ```bash
   composer config github-oauth.github.com your_token_here
   ```

3. **Install the package:**

   ```bash
   composer require greelogix/myfatoorah-laravel
   ```

#### Option B: Install from Packagist (When Published)

```bash
composer require greelogix/myfatoorah-laravel
```

### Step 2: Publish and Run Migrations

```bash
# Publish migrations
php artisan vendor:publish --tag=myfatoorah-migrations

# Run migrations
php artisan migrate
```

This will create the following table:
- `myfatoorah_payments` - Stores payment transactions

### Step 3: Configure Environment Variables

Add the following to your `.env` file:

```env
MYFATOORAH_API_KEY=your_api_key_here
MYFATOORAH_BASE_URL=https://apitest.myfatoorah.com
MYFATOORAH_TEST_MODE=true
MYFATOORAH_WEBHOOK_SECRET=your_webhook_secret_here
MYFATOORAH_CURRENCY=KWD
MYFATOORAH_LANGUAGE=en
MYFATOORAH_COUNTRY_ISO=KWT
```

**For Production:**
- Set `MYFATOORAH_BASE_URL=https://api.myfatoorah.com`
- Set `MYFATOORAH_TEST_MODE=false`
- Use your production API key

### Step 4: Publish Config File (Optional)

If you want to customize the config file:

```bash
php artisan vendor:publish --tag=myfatoorah-config
```

This will publish `config/myfatoorah.php` to your app's config directory.

### Step 5: Configure Webhook (Production)

1. **Get webhook URL:**
   ```
   https://yoursite.com/myfatoorah/webhook
   ```

2. **Configure in MyFatoorah Dashboard:**
   - Log in to your MyFatoorah dashboard
   - Navigate to Settings > Webhooks
   - Add webhook URL: `https://yoursite.com/myfatoorah/webhook`
   - Copy the webhook secret

3. **Add webhook secret to `.env`:**
   ```env
   MYFATOORAH_WEBHOOK_SECRET=your_webhook_secret_here
   ```

## Usage

### Get Payment Methods

```php
use Greelogix\MyFatoorah\Facades\MyFatoorah;

try {
    $response = MyFatoorah::getPaymentMethods(
        amount: 100.00,
        currency: 'KWD'
    );

    // Payment methods are in $response['Data']['PaymentMethods']
    $paymentMethods = $response['Data']['PaymentMethods'];
    
    foreach ($paymentMethods as $method) {
        echo $method['PaymentMethodEn'] . "\n";
    }
} catch (\Greelogix\MyFatoorah\Exceptions\MyFatoorahException $e) {
    // Handle error
    return back()->with('error', $e->getMessage());
}
```

### Basic Payment Initiation

```php
use Greelogix\MyFatoorah\Facades\MyFatoorah;

try {
    $response = MyFatoorah::initiatePayment([
        'amount' => 100.00,
        'currency' => 'KWD',
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_mobile' => '+96512345678', // Automatically formatted
        'language' => 'en', // or 'ar'
        'country_iso' => 'KWT', // Optional, uses config default if not provided
        'callback_url' => 'https://yoursite.com/payment/callback',
        'error_url' => 'https://yoursite.com/payment/error',
    ]);

    // Redirect user to payment page
    $paymentUrl = $response['Data']['InvoiceURL'];
    $invoiceId = $response['Data']['InvoiceId'];
  
    return redirect($paymentUrl);
  
} catch (\Greelogix\MyFatoorah\Exceptions\MyFatoorahException $e) {
    // Handle error
    return back()->with('error', $e->getMessage());
}
```

### Execute Payment

```php
$response = MyFatoorah::executePayment($paymentMethodId, [
    'amount' => 100.00,
    'currency' => 'KWD',
    'customer_name' => 'John Doe',
    'customer_email' => 'john@example.com',
    'customer_mobile' => '+96512345678',
    'language' => 'en',
    'country_iso' => 'KWT',
    'callback_url' => 'https://yoursite.com/payment/callback',
    'error_url' => 'https://yoursite.com/payment/error',
]);
```

### Mobile Number Formatting

The package automatically formats mobile numbers. It handles:
- Spaces, dashes, and parentheses removal
- Country code formatting (00 → +)
- Proper international format

Examples:
- `00965 1234 5678` → `+96512345678`
- `+965-1234-5678` → `+96512345678`
- `(965) 1234-5678` → `+96512345678`

### Recurring Payments

```php
$response = MyFatoorah::initiatePayment([
    'amount' => 100.00,
    'currency' => 'KWD',
    'customer_name' => 'John Doe',
    'customer_email' => 'john@example.com',
    'customer_mobile' => '+96512345678',
    'recurring' => true,
    'recurring_type' => 'Monthly',
    'recurring_cycle' => 'Monthly',
    'recurring_cycle_count' => 12,
    'day_of_month' => 1,
    'callback_url' => 'https://yoursite.com/payment/callback',
    'error_url' => 'https://yoursite.com/payment/error',
]);
```

### Using Models

```php
use Greelogix\MyFatoorah\Models\MyFatoorahPayment;

// Check payment status
$payment = MyFatoorahPayment::find($id);
if ($payment->isSuccessful()) {
    // Payment successful
}

// Get payment by invoice ID
$payment = MyFatoorahPayment::where('invoice_id', $invoiceId)->first();

// Check payment states
$payment->isSuccessful(); // Returns true if Paid or Success
$payment->isPending();   // Returns true if Pending or InProgress
$payment->isFailed();    // Returns true if Failed, Error, or Canceled
```

### Available Service Methods

```php
// Get payment methods
$methods = MyFatoorah::getPaymentMethods($amount, $currency);

// Get payment status
$status = MyFatoorah::getPaymentStatus($paymentId);

// Get invoice status
$invoice = MyFatoorah::getInvoiceStatus($invoiceId);

// Cancel invoice
$result = MyFatoorah::cancelInvoice($invoiceId);

// Create recurring payment
$recurring = MyFatoorah::createRecurringPayment([...]);

// Execute recurring payment
$result = MyFatoorah::executeRecurringPayment($recurringId, [...]);
```

## Webhooks

### Webhook URL

The package automatically registers a webhook route:

```
POST /myfatoorah/webhook
```

This route is **CSRF exempt** and handles payment status updates from MyFatoorah.

### Webhook Events

The package fires the following event when payment status is updated:

```php
\Greelogix\MyFatoorah\Events\PaymentStatusUpdated
```

You can listen to this event in your `EventServiceProvider`:

```php
protected $listen = [
    \Greelogix\MyFatoorah\Events\PaymentStatusUpdated::class => [
        // Your listeners here
    ],
];
```

### Webhook Payload Format

The webhook receives payment status updates and automatically:
- Validates webhook signature (if secret is configured)
- Updates payment records in database
- Fires `PaymentStatusUpdated` event

## Configuration

All configuration is done via `.env` file or `config/myfatoorah.php`:

```php
'api_key' => env('MYFATOORAH_API_KEY', ''),
'base_url' => env('MYFATOORAH_BASE_URL', 'https://apitest.myfatoorah.com'),
'test_mode' => env('MYFATOORAH_TEST_MODE', true),
'webhook_secret' => env('MYFATOORAH_WEBHOOK_SECRET', ''),
'currency' => env('MYFATOORAH_CURRENCY', 'KWD'),
'language' => env('MYFATOORAH_LANGUAGE', 'en'),
'country_iso' => env('MYFATOORAH_COUNTRY_ISO', 'KWT'),
```

## Testing

### Test Mode

1. Set `MYFATOORAH_TEST_MODE=true` in `.env`
2. Use test API key from MyFatoorah dashboard
3. Use test base URL: `https://apitest.myfatoorah.com`

### Test Cards

Refer to [MyFatoorah Test Cards Documentation](https://docs.myfatoorah.com/) for test card numbers.

## Troubleshooting

### Settings Not Updating

```bash
php artisan config:clear
php artisan cache:clear
```

### Migration Issues

```bash
# Rollback and re-run migrations
php artisan migrate:rollback
php artisan migrate
```

### Payment Methods Not Loading

1. Check API key is correct in `.env`
2. Verify base URL is correct (test vs production)
3. Check Laravel logs for API errors

## Production Checklist

- [ ] Set `MYFATOORAH_TEST_MODE=false` in `.env`
- [ ] Use production API key from MyFatoorah dashboard
- [ ] Set `MYFATOORAH_BASE_URL=https://api.myfatoorah.com`
- [ ] Configure webhook URL in MyFatoorah dashboard
- [ ] Add webhook secret to `.env`
- [ ] Test payment flow end-to-end
- [ ] Verify webhook is receiving updates
- [ ] Monitor payment logs

## Security

- Webhook route validates signature using webhook secret
- Admin routes are protected with authentication middleware
- CSRF protection enabled (webhook route is exempt)

## License

MIT

## Support

For issues and questions:

- Check the [MyFatoorah Official Documentation](https://docs.myfatoorah.com/)
- Review package documentation
- Contact: asad.ali@greelogix.com

## Changelog

### Version 2.0.0
- Simplified package - removed admin panel and database settings
- Removed payment methods and site settings management
- Configuration now via `.env` only
- Enhanced mobile number formatting
- Added country ISO support
- Improved payload formatting

### Version 1.0.0
- Initial release
- Complete MyFatoorah API integration
- Admin panel for settings and payment methods
- Webhook handling
- Recurring payments support
- Platform-specific payment method activation
