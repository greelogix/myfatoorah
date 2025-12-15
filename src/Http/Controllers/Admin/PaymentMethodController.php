<?php

namespace Greelogix\MyFatoorah\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Greelogix\MyFatoorah\Facades\MyFatoorah;
use Greelogix\MyFatoorah\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    /**
     * Display payment methods
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order')->orderBy('payment_method_en')->get();
        return view('myfatoorah::admin.payment-methods.index', compact('paymentMethods'));
    }

    /**
     * Toggle platform status for payment method
     */
    public function toggleStatus(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'platform' => 'required|in:ios,android,web',
            'status' => 'required|boolean',
        ]);

        $column = 'is_active_' . $request->platform;
        $paymentMethod->update([$column => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status' => $paymentMethod->$column,
        ]);
    }

    /**
     * Sync payment methods from MyFatoorah API using InitiatePayment
     */
    public function sync()
    {
        try {
            // Use InitiatePayment to get payment methods
            $response = MyFatoorah::initiatePayment([
                'amount' => 1,
                'currency' => config('myfatoorah.currency', 'KWD'),
                'customer_name' => 'Sync',
                'customer_email' => 'sync@example.com',
                'customer_mobile' => '',
            ]);

            $paymentMethods = $response['Data']['PaymentMethods'] ?? [];

            foreach ($paymentMethods as $method) {
                $existing = PaymentMethod::where('payment_method_id', $method['PaymentMethodId'])->first();
                
                $data = [
                    'payment_method_en' => $method['PaymentMethodEn'] ?? null,
                    'payment_method_ar' => $method['PaymentMethodAr'] ?? null,
                    'payment_method_code' => $method['PaymentMethodCode'] ?? null,
                    'is_direct_payment' => $method['IsDirectPayment'] ?? false,
                    'service_charge' => $method['ServiceCharge'] ?? 0,
                    'total_amount' => $method['TotalAmount'] ?? null,
                    'currency_iso' => $method['CurrencyIso'] ?? 'KWD',
                    'image_url' => $method['ImageUrl'] ?? null,
                ];

                // Preserve platform statuses if method already exists
                if ($existing) {
                    $data['is_active_ios'] = $existing->is_active_ios;
                    $data['is_active_android'] = $existing->is_active_android;
                    $data['is_active_web'] = $existing->is_active_web;
                } else {
                    $data['is_active'] = true;
                    $data['is_active_ios'] = true;
                    $data['is_active_android'] = true;
                    $data['is_active_web'] = true;
                }

                PaymentMethod::updateOrCreate(
                    ['payment_method_id' => $method['PaymentMethodId']],
                    $data
                );
            }

            return redirect()->route('myfatoorah.admin.payment-methods.index')
                ->with('success', 'Payment methods synced successfully.');
        } catch (\Exception $e) {
            return redirect()->route('myfatoorah.admin.payment-methods.index')
                ->with('error', 'Failed to sync payment methods: ' . $e->getMessage());
        }
    }
}

