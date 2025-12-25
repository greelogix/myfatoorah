<?php

namespace Greelogix\MyFatoorah\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Greelogix\MyFatoorah\Facades\MyFatoorah;
use Greelogix\MyFatoorah\Models\MyFatoorahPayment;

class WebhookController extends Controller
{
    /**
     * Handle MyFatoorah webhook
     */
    public function handle(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('MyFatoorah Webhook Received', ['data' => $data]);

            // Validate webhook signature if secret is configured
            if (Config::get('myfatoorah.webhook_secret')) {
                if (!$this->validateWebhookSignature($request)) {
                    Log::warning('MyFatoorah Webhook: Invalid signature', ['data' => $data]);
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            // Extract payment information
            $invoiceId = $data['Data']['InvoiceId'] ?? $data['InvoiceId'] ?? null;
            $paymentId = $data['Data']['PaymentId'] ?? $data['PaymentId'] ?? null;

            if (!$invoiceId && !$paymentId) {
                Log::warning('MyFatoorah Webhook: Missing invoice/payment ID', ['data' => $data]);
                return response()->json(['error' => 'Missing invoice/payment ID'], 400);
            }

            // Get payment status from MyFatoorah
            $paymentStatus = null;
            if ($invoiceId) {
                $paymentStatus = MyFatoorah::getInvoiceStatus($invoiceId);
            } elseif ($paymentId) {
                $paymentStatus = MyFatoorah::getPaymentStatus($paymentId);
            }

            // Find or create payment record
            $payment = MyFatoorahPayment::where('invoice_id', $invoiceId)
                ->orWhere('payment_id', $paymentId)
                ->first();

            if ($payment && $paymentStatus) {
                $this->updatePaymentFromStatus($payment, $paymentStatus);
            } elseif ($paymentStatus) {
                $payment = $this->createPaymentFromStatus($paymentStatus);
            }

            if ($payment) {
                $payment->update([
                    'webhook_data' => $data,
                ]);
            }

            // Fire event for payment status update
            event(new \Greelogix\MyFatoorah\Events\PaymentStatusUpdated($payment, $data));

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('MyFatoorah Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all(),
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Validate webhook signature
     */
    protected function validateWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-MyFatoorah-Signature');
        $secret = Config::get('myfatoorah.webhook_secret');

        if (!$signature || !$secret) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Update payment from status response
     */
    protected function updatePaymentFromStatus(MyFatoorahPayment $payment, array $statusData): void
    {
        $invoiceData = $statusData['Data']['InvoiceTransactions'][0] ?? $statusData['Data'] ?? [];

        $payment->update([
            'transaction_status' => $invoiceData['TransactionStatus'] ?? $statusData['Data']['InvoiceStatus'] ?? null,
            'transaction_date' => isset($invoiceData['TransactionDate']) ? date('Y-m-d H:i:s', strtotime($invoiceData['TransactionDate'])) : null,
            'payment_gateway' => $invoiceData['PaymentGateway'] ?? null,
            'reference_id' => $invoiceData['ReferenceId'] ?? null,
            'track_id' => $invoiceData['TrackId'] ?? null,
            'authorization_id' => $invoiceData['AuthorizationId'] ?? null,
            'card_number' => $invoiceData['CardNumber'] ?? null,
            'card_type' => $invoiceData['CardType'] ?? null,
            'raw_response' => $statusData,
        ]);
    }

    /**
     * Create payment from status response
     */
    protected function createPaymentFromStatus(array $statusData): ?MyFatoorahPayment
    {
        $invoiceData = $statusData['Data'] ?? [];
        $transactionData = $invoiceData['InvoiceTransactions'][0] ?? [];

        return MyFatoorahPayment::create([
            'invoice_id' => $invoiceData['InvoiceId'] ?? null,
            'payment_id' => $transactionData['PaymentId'] ?? null,
            'payment_method_id' => $transactionData['PaymentMethodId'] ?? null,
            'payment_method' => $transactionData['PaymentMethod'] ?? null,
            'customer_name' => $invoiceData['CustomerName'] ?? '',
            'customer_email' => $invoiceData['CustomerEmail'] ?? '',
            'customer_mobile' => $invoiceData['CustomerMobile'] ?? '',
            'amount' => $invoiceData['InvoiceValue'] ?? 0,
            'currency' => $invoiceData['Currency'] ?? Config::get('myfatoorah.currency', 'KWD'),
            'invoice_value' => $invoiceData['InvoiceValue'] ?? null,
            'invoice_display_value' => $invoiceData['InvoiceDisplayValue'] ?? null,
            'transaction_status' => $transactionData['TransactionStatus'] ?? $invoiceData['InvoiceStatus'] ?? null,
            'transaction_date' => isset($transactionData['TransactionDate']) ? date('Y-m-d H:i:s', strtotime($transactionData['TransactionDate'])) : null,
            'payment_gateway' => $transactionData['PaymentGateway'] ?? null,
            'reference_id' => $transactionData['ReferenceId'] ?? null,
            'track_id' => $transactionData['TrackId'] ?? null,
            'authorization_id' => $transactionData['AuthorizationId'] ?? null,
            'card_number' => $transactionData['CardNumber'] ?? null,
            'card_type' => $transactionData['CardType'] ?? null,
            'invoice_url' => $invoiceData['InvoiceURL'] ?? null,
            'invoice_items' => $invoiceData['InvoiceItems'] ?? null,
            'customer_address' => $invoiceData['CustomerAddress'] ?? null,
            'raw_response' => $statusData,
        ]);
    }
}

