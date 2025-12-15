<?php

namespace Greelogix\MyFatoorah\Database\Seeders;

use Illuminate\Database\Seeder;
use Greelogix\MyFatoorah\Facades\MyFatoorah;
use Greelogix\MyFatoorah\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Get payment methods from InitiatePayment API
            $response = MyFatoorah::initiatePayment([
                'amount' => 1,
                'currency' => config('myfatoorah.currency', 'KWD'),
                'customer_name' => 'Seeder',
                'customer_email' => 'seeder@example.com',
                'customer_mobile' => '',
            ]);

            $paymentMethods = $response['Data']['PaymentMethods'] ?? [];

            foreach ($paymentMethods as $method) {
                PaymentMethod::updateOrCreate(
                    ['payment_method_id' => $method['PaymentMethodId']],
                    [
                        'payment_method_en' => $method['PaymentMethodEn'] ?? null,
                        'payment_method_ar' => $method['PaymentMethodAr'] ?? null,
                        'payment_method_code' => $method['PaymentMethodCode'] ?? null,
                        'is_direct_payment' => $method['IsDirectPayment'] ?? false,
                        'service_charge' => $method['ServiceCharge'] ?? 0,
                        'total_amount' => $method['TotalAmount'] ?? null,
                        'currency_iso' => $method['CurrencyIso'] ?? 'KWD',
                        'image_url' => $method['ImageUrl'] ?? null,
                        'is_active' => true,
                        'is_active_ios' => true,
                        'is_active_android' => true,
                        'is_active_web' => true,
                        'sort_order' => 0,
                    ]
                );
            }

            $this->command->info('Payment methods seeded successfully.');
        } catch (\Exception $e) {
            $this->command->error('Failed to seed payment methods: ' . $e->getMessage());
        }
    }
}

