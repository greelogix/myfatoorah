# MyFatoorah Laravel Package

A production-ready Laravel package for MyFatoorah payment gateway integration with support for all payment methods, recurring payments, and platform-specific activation (iOS, Android, Web).

## Features

- ✅ Complete MyFatoorah API integration
- ✅ All payment methods support
- ✅ Recurring payments
- ✅ Platform-specific activation (iOS/Android/Web)
- ✅ Webhook handling with signature validation
- ✅ Admin panel for payment methods and settings
- ✅ Site settings-driven configuration (database-first approach)
- ✅ Laravel HTTP Client (no external dependencies)
- ✅ Laravel 10.x and 11.x compatible
- ✅ Auto-discovery enabled
- ✅ Comprehensive error handling
- ✅ Payment status tracking
- ✅ Database models with relationships

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

This will create the following tables:
- `myfatoorah_payment_methods` - Stores available payment methods
- `myfatoorah_payments` - Stores payment transactions
- `myfatoorah_site_settings` - Stores configuration settings

### Step 3: Seed Default Settings

```bash
php artisan db:seed --class="Greelogix\MyFatoorah\Database\Seeders\DefaultSettingsSeeder"
```

This will populate default MyFatoorah settings with test values:
- API Key (test key)
- Test Mode: enabled
- Country ISO: KWT
- Save Card: enabled
- Register Apple Pay: enabled
- Currency: KWD
- Language: en

**Note:** Settings will also be automatically created when you first visit the admin settings page.

### Step 4: Configure Settings in Admin Panel

**⚠️ Important: All MyFatoorah settings are managed through the admin panel (site settings table), not directly from `.env`!**

1. **Ensure authentication is configured** (admin routes require `auth` middleware)

2. **Visit the admin settings page:**
   ```
   /admin/myfatoorah/settings
   ```

3. **Configure your settings:**
   - **MyFatoorah API Key:** Your production or test API key from MyFatoorah Dashboard
   - **Base URL:** 
     - Test: `https://apitest.myfatoorah.com`
     - Production: `https://api.myfatoorah.com`
   - **Test Mode:** Yes for testing, No for production
   - **Webhook Secret:** From MyFatoorah Dashboard > Settings > Webhooks
   - **Currency:** Default currency (KWD, USD, SAR, etc.)
   - **Language:** Default language (en or ar)
   - **Country ISO:** Country code (KWT, SAU, etc.)
   - **Save Card:** Allow saving payment cards
   - **Register Apple Pay:** Enable Apple Pay registration

4. **Click "Save Settings"**

**Note:** After changing settings, clear cache:

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 5: Seed Payment Methods

**Option A: Using Admin Panel (Recommended)**

1. Visit `/admin/myfatoorah/payment-methods`
2. Click "Sync from API" button
3. Payment methods will be fetched from MyFatoorah API and stored in database

**Option B: Using Seeder**

```bash
php artisan db:seed --class="Greelogix\MyFatoorah\Database\Seeders\PaymentMethodSeeder"
```

### Step 6: Configure Webhook (Production)

1. **Get webhook URL:**
   ```
   https://yoursite.com/myfatoorah/webhook
   ```

2. **Configure in MyFatoorah Dashboard:**
   - Log in to your MyFatoorah dashboard
   - Navigate to Settings > Webhooks
   - Add webhook URL: `https://yoursite.com/myfatoorah/webhook`
   - Copy the webhook secret

3. **Add webhook secret to admin panel:**
   - Visit `/admin/myfatoorah/settings`
   - Enter the webhook secret
   - Save settings

## Configuration Priority

The package uses a **database-first configuration approach**:

1. **Primary Source:** Values from `myfatoorah_site_settings` table (managed via `/admin/myfatoorah/settings`)
2. **Fallback:** `config/myfatoorah.php` values
3. **Last Fallback:** `.env` values (e.g., `MYFATOORAH_API_KEY`, `MYFATOORAH_BASE_URL`)

**Best Practice:** Manage all settings through the admin panel for production. Use `.env` only as a backup for local/test environments.

### Optional: Publish Config File

If you want to customize the config file:

```bash
php artisan vendor:publish --tag=myfatoorah-config
```

This will publish `config/myfatoorah.php` to your app's config directory.

## Usage

### Basic Payment Initiation

```php
use Greelogix\MyFatoorah\Facades\MyFatoorah;

try {
    $response = MyFatoorah::initiatePayment([
        'amount' => 100.00,
        'currency' => 'KWD',
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_mobile' => '+96512345678',
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
    'callback_url' => 'https://yoursite.com/payment/callback',
    'error_url' => 'https://yoursite.com/payment/error',
]);
```

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
use Greelogix\MyFatoorah\Models\PaymentMethod;

// Get active payment methods for iOS
$iosMethods = PaymentMethod::activeForPlatform('ios')->get();

// Get active payment methods for Android
$androidMethods = PaymentMethod::activeForPlatform('android')->get();

// Get active payment methods for Web
$webMethods = PaymentMethod::activeForPlatform('web')->get();

// Check payment status
$payment = MyFatoorahPayment::find($id);
if ($payment->isSuccessful()) {
    // Payment successful
}

// Get payment by invoice ID
$payment = MyFatoorahPayment::where('invoice_id', $invoiceId)->first();
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

## Admin Panel

### Access Admin Routes

- **Settings:** `/admin/myfatoorah/settings` - Configure API key and all settings
- **Payment Methods:** `/admin/myfatoorah/payment-methods` - Manage payment methods and sync from API

**Note:** Admin routes are protected with `auth` middleware. Ensure you have authentication configured in your Laravel application.

### Payment Methods Management

- **Sync from API:** Fetches all available payment methods from MyFatoorah API
- **Toggle Status:** Enable/disable payment methods
- **Platform Activation:** Activate payment methods for specific platforms (iOS, Android, Web)

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

## Testing

### Test Mode

1. Set **Test Mode** to `true` in admin panel (`/admin/myfatoorah/settings`)
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

### Payment Methods Not Syncing

1. Check API key is correct in admin panel
2. Verify base URL is correct (test vs production)
3. Check Laravel logs for API errors

## Production Checklist

- [ ] Set **Test Mode** to `false` in admin panel
- [ ] Use production API key from MyFatoorah dashboard
- [ ] Set base URL to `https://api.myfatoorah.com`
- [ ] Configure webhook URL in MyFatoorah dashboard
- [ ] Add webhook secret to admin panel
- [ ] Test payment flow end-to-end
- [ ] Verify webhook is receiving updates
- [ ] Monitor payment logs

## Security

- Webhook route validates signature using webhook secret
- API keys are stored encrypted in database
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

### Version 1.0.0
- Initial release
- Complete MyFatoorah API integration
- Admin panel for settings and payment methods
- Webhook handling
- Recurring payments support
- Platform-specific payment method activation
