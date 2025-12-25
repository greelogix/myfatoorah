# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2024-12-XX

### Removed
- Admin panel for settings management
- Admin panel for payment methods management
- Payment methods database table and model
- Site settings database table and model
- Payment methods and settings seeders
- Database-first configuration approach

### Changed
- Configuration now uses `.env` file only (simpler approach)
- Simplified package structure - removed unnecessary complexity
- Enhanced mobile number formatting (automatic formatting)
- Added country ISO support in payment payloads
- Improved payload formatting with automatic number/country/language handling

### Added
- Automatic mobile number formatting (handles spaces, dashes, country codes)
- Country ISO code support in payment methods
- Better documentation for simplified usage

### Fixed
- All config() calls now use Config facade properly
- CSRF middleware reference in routes

## [1.0.0] - 2024-01-01

### Added
- Complete MyFatoorah API integration
- Admin panel for settings management (`/admin/myfatoorah/settings`)
- Admin panel for payment methods management (`/admin/myfatoorah/payment-methods`)
- Site settings-driven configuration (database-first approach)
- Webhook handling with signature validation
- Recurring payments support
- Platform-specific payment method activation (iOS, Android, Web)
- Payment status tracking with database models
- Default settings seeder with test values
- Payment method seeder
- Comprehensive error handling
- Laravel 10.x and 11.x compatibility
- Auto-discovery support
- Facade for easy access (`MyFatoorah`)
- Database migrations for:
  - Payment methods table
  - Payments table
  - Site settings table
- Models with relationships:
  - `MyFatoorahPayment`
  - `PaymentMethod`
  - `SiteSetting`
- Events:
  - `PaymentStatusUpdated`
- Service methods:
  - `initiatePayment()`
  - `executePayment()`
  - `sendPayment()`
  - `getPaymentMethods()`
  - `getPaymentStatus()`
  - `getInvoiceStatus()`
  - `cancelInvoice()`
  - `createRecurringPayment()`
  - `executeRecurringPayment()`

### Security
- Webhook signature validation
- CSRF protection (webhook route exempt)
- Admin routes protected with authentication middleware
- Secure API key storage in database
