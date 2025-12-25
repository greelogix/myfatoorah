<?php

namespace Greelogix\MyFatoorah\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Greelogix\MyFatoorah\Exceptions\MyFatoorahException;

class MyFatoorahService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected bool $testMode;

    public function __construct(string $apiKey, string $baseUrl, bool $testMode = true)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->testMode = $testMode;
    }

    /**
     * Get payment methods using InitiatePayment
     */
    public function getPaymentMethods(float $amount = 1.0, string $currency = 'KWD'): array
    {
        $payload = [
            'InvoiceAmount' => $amount,
            'CurrencyIso' => $currency,
            'CustomerName' => 'Payment Methods',
            'CustomerEmail' => 'methods@example.com',
            'CustomerMobile' => '',
        ];

        return $this->makeRequest('POST', '/v2/InitiatePayment', $payload);
    }

    /**
     * Initiate payment
     */
    public function initiatePayment(array $data): array
    {
        $payload = [
            'InvoiceAmount' => $data['amount'],
            'CurrencyIso' => $data['currency'] ?? Config::get('myfatoorah.currency', 'KWD'),
            'CustomerName' => $data['customer_name'],
            'CustomerEmail' => $data['customer_email'],
            'CustomerMobile' => $this->formatMobileNumber($data['customer_mobile'] ?? ''),
            'Language' => $data['language'] ?? Config::get('myfatoorah.language', 'en'),
            'CallBackUrl' => $data['callback_url'] ?? '',
            'ErrorUrl' => $data['error_url'] ?? '',
        ];

        // Add country ISO if provided
        if (isset($data['country_iso'])) {
            $payload['CountryCode'] = $data['country_iso'];
        } elseif (Config::get('myfatoorah.country_iso')) {
            $payload['CountryCode'] = Config::get('myfatoorah.country_iso');
        }

        // Add invoice items if provided
        if (isset($data['invoice_items'])) {
            $payload['InvoiceItems'] = $data['invoice_items'];
        }

        // Add payment method ID if specified
        if (isset($data['payment_method_id'])) {
            $payload['PaymentMethodId'] = $data['payment_method_id'];
        }

        // Add recurring payment data if provided
        if (isset($data['recurring']) && $data['recurring'] === true) {
            $payload['RecurringModel'] = [
                'RecurringType' => $data['recurring_type'] ?? 'Monthly',
                'DayOfMonth' => $data['day_of_month'] ?? 1,
                'DayOfWeek' => $data['day_of_week'] ?? 'Monday',
                'MonthOfYear' => $data['month_of_year'] ?? 1,
                'RecurringCycle' => $data['recurring_cycle'] ?? Config::get('myfatoorah.recurring.default_cycle', 'Monthly'),
                'RecurringCycleCount' => $data['recurring_cycle_count'] ?? Config::get('myfatoorah.recurring.default_cycle_count', 12),
            ];
        }

        return $this->makeRequest('POST', '/v2/InitiatePayment', $payload);
    }

    /**
     * Execute payment
     */
    public function executePayment(string $paymentId, array $data = []): array
    {
        $payload = [
            'PaymentMethodId' => $paymentId,
            'CustomerName' => $data['customer_name'] ?? '',
            'CustomerEmail' => $data['customer_email'] ?? '',
            'CustomerMobile' => $this->formatMobileNumber($data['customer_mobile'] ?? ''),
            'InvoiceValue' => $data['amount'],
            'DisplayCurrencyIso' => $data['currency'] ?? Config::get('myfatoorah.currency', 'KWD'),
            'CallBackUrl' => $data['callback_url'] ?? '',
            'ErrorUrl' => $data['error_url'] ?? '',
            'Language' => $data['language'] ?? Config::get('myfatoorah.language', 'en'),
        ];

        // Add country ISO if provided
        if (isset($data['country_iso'])) {
            $payload['CountryCode'] = $data['country_iso'];
        } elseif (Config::get('myfatoorah.country_iso')) {
            $payload['CountryCode'] = Config::get('myfatoorah.country_iso');
        }

        // Add invoice items if provided
        if (isset($data['invoice_items'])) {
            $payload['InvoiceItems'] = $data['invoice_items'];
        }

        // Add card token for recurring payments
        if (isset($data['card_token'])) {
            $payload['CardToken'] = $data['card_token'];
        }

        // Add recurring payment data if provided
        if (isset($data['recurring']) && $data['recurring'] === true) {
            $payload['RecurringModel'] = [
                'RecurringType' => $data['recurring_type'] ?? 'Monthly',
                'DayOfMonth' => $data['day_of_month'] ?? 1,
                'DayOfWeek' => $data['day_of_week'] ?? 'Monday',
                'MonthOfYear' => $data['month_of_year'] ?? 1,
                'RecurringCycle' => $data['recurring_cycle'] ?? Config::get('myfatoorah.recurring.default_cycle', 'Monthly'),
                'RecurringCycleCount' => $data['recurring_cycle_count'] ?? Config::get('myfatoorah.recurring.default_cycle_count', 12),
            ];
        }

        return $this->makeRequest('POST', '/v2/ExecutePayment', $payload);
    }

    /**
     * Send payment (direct payment)
     */
    public function sendPayment(array $data): array
    {
        $payload = [
            'PaymentMethodId' => $data['payment_method_id'],
            'InvoiceValue' => $data['amount'],
            'CurrencyIso' => $data['currency'] ?? Config::get('myfatoorah.currency', 'KWD'),
            'CustomerName' => $data['customer_name'],
            'CustomerEmail' => $data['customer_email'],
            'CustomerMobile' => $this->formatMobileNumber($data['customer_mobile'] ?? ''),
            'Language' => $data['language'] ?? Config::get('myfatoorah.language', 'en'),
            'CallBackUrl' => $data['callback_url'] ?? '',
            'ErrorUrl' => $data['error_url'] ?? '',
        ];

        // Add country ISO if provided
        if (isset($data['country_iso'])) {
            $payload['CountryCode'] = $data['country_iso'];
        } elseif (Config::get('myfatoorah.country_iso')) {
            $payload['CountryCode'] = Config::get('myfatoorah.country_iso');
        }

        // Add invoice items if provided
        if (isset($data['invoice_items'])) {
            $payload['InvoiceItems'] = $data['invoice_items'];
        }

        // Add card details for direct payment
        if (isset($data['card'])) {
            $payload['CardNumber'] = $data['card']['number'];
            $payload['CardExpiryMonth'] = $data['card']['expiry_month'];
            $payload['CardExpiryYear'] = $data['card']['expiry_year'];
            $payload['CardSecurityCode'] = $data['card']['cvv'];
            $payload['CardHolderName'] = $data['card']['holder_name'];
        }

        // Add recurring payment data if provided
        if (isset($data['recurring']) && $data['recurring'] === true) {
            $payload['RecurringModel'] = [
                'RecurringType' => $data['recurring_type'] ?? 'Monthly',
                'DayOfMonth' => $data['day_of_month'] ?? 1,
                'DayOfWeek' => $data['day_of_week'] ?? 'Monday',
                'MonthOfYear' => $data['month_of_year'] ?? 1,
                'RecurringCycle' => $data['recurring_cycle'] ?? Config::get('myfatoorah.recurring.default_cycle', 'Monthly'),
                'RecurringCycleCount' => $data['recurring_cycle_count'] ?? Config::get('myfatoorah.recurring.default_cycle_count', 12),
            ];
        }

        return $this->makeRequest('POST', '/v2/SendPayment', $payload);
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $paymentId): array
    {
        return $this->makeRequest('GET', "/v2/GetPaymentStatus/{$paymentId}");
    }

    /**
     * Get invoice status
     */
    public function getInvoiceStatus(string $invoiceId): array
    {
        return $this->makeRequest('GET', "/v2/GetInvoiceStatus/{$invoiceId}");
    }

    /**
     * Cancel invoice
     */
    public function cancelInvoice(string $invoiceId): array
    {
        return $this->makeRequest('POST', "/v2/CancelInvoice/{$invoiceId}");
    }

    /**
     * Create recurring payment
     */
    public function createRecurringPayment(array $data): array
    {
        $payload = [
            'InvoiceValue' => $data['amount'],
            'CurrencyIso' => $data['currency'] ?? Config::get('myfatoorah.currency', 'KWD'),
            'CustomerName' => $data['customer_name'],
            'CustomerEmail' => $data['customer_email'],
            'CustomerMobile' => $this->formatMobileNumber($data['customer_mobile'] ?? ''),
            'Language' => $data['language'] ?? Config::get('myfatoorah.language', 'en'),
            'RecurringModel' => [
                'RecurringType' => $data['recurring_type'] ?? 'Monthly',
                'DayOfMonth' => $data['day_of_month'] ?? 1,
                'DayOfWeek' => $data['day_of_week'] ?? 'Monday',
                'MonthOfYear' => $data['month_of_year'] ?? 1,
                'RecurringCycle' => $data['recurring_cycle'] ?? Config::get('myfatoorah.recurring.default_cycle', 'Monthly'),
                'RecurringCycleCount' => $data['recurring_cycle_count'] ?? Config::get('myfatoorah.recurring.default_cycle_count', 12),
            ],
        ];

        // Add country ISO if provided
        if (isset($data['country_iso'])) {
            $payload['CountryCode'] = $data['country_iso'];
        } elseif (Config::get('myfatoorah.country_iso')) {
            $payload['CountryCode'] = Config::get('myfatoorah.country_iso');
        }

        // Add invoice items if provided
        if (isset($data['invoice_items'])) {
            $payload['InvoiceItems'] = $data['invoice_items'];
        }

        return $this->makeRequest('POST', '/v2/InitiateRecurringPayment', $payload);
    }

    /**
     * Execute recurring payment
     */
    public function executeRecurringPayment(string $recurringId, array $data): array
    {
        $payload = [
            'RecurringId' => $recurringId,
            'InvoiceValue' => $data['amount'],
            'CurrencyIso' => $data['currency'] ?? Config::get('myfatoorah.currency', 'KWD'),
        ];

        return $this->makeRequest('POST', '/v2/ExecuteRecurringPayment', $payload);
    }

    /**
     * Make HTTP request to MyFatoorah API
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->{strtolower($method)}($url, $method === 'GET' ? [] : $data);

            $responseData = $response->json();

            if (!$response->successful()) {
                $errorMessage = $responseData['Message'] ?? 'Unknown error occurred';
                $errorCode = $responseData['ErrorCode'] ?? $response->status();
                
                Log::error('MyFatoorah API Error', [
                    'url' => $url,
                    'method' => $method,
                    'status' => $response->status(),
                    'error' => $responseData,
                ]);

                throw new MyFatoorahException($errorMessage, $errorCode);
            }

            if (isset($responseData['IsSuccess']) && !$responseData['IsSuccess']) {
                $errorMessage = $responseData['Message'] ?? 'Payment request failed';
                $errorCode = $responseData['ValidationErrors'][0]['Error'] ?? 0;
                
                throw new MyFatoorahException($errorMessage, $errorCode);
            }

            return $responseData;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('MyFatoorah Request Exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw new MyFatoorahException('Failed to connect to MyFatoorah API: ' . $e->getMessage(), 0);
        } catch (\Exception $e) {
            Log::error('MyFatoorah Exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw new MyFatoorahException('An error occurred: ' . $e->getMessage(), 0);
        }
    }

    /**
     * Format mobile number for MyFatoorah API
     * Removes spaces, dashes, and ensures proper format
     */
    protected function formatMobileNumber(?string $mobile): string
    {
        if (empty($mobile)) {
            return '';
        }

        // Remove spaces, dashes, and parentheses
        $mobile = preg_replace('/[\s\-\(\)]/', '', $mobile);

        // If starts with +, keep it; otherwise ensure country code format
        if (!str_starts_with($mobile, '+')) {
            // If starts with 00, replace with +
            if (str_starts_with($mobile, '00')) {
                $mobile = '+' . substr($mobile, 2);
            }
        }

        return $mobile;
    }
}

