<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('myfatoorah_payments', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id')->unique()->nullable();
            $table->string('payment_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_mobile')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('KWD');
            $table->decimal('invoice_value', 10, 2)->nullable();
            $table->decimal('invoice_display_value', 10, 2)->nullable();
            $table->string('transaction_status')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('track_id')->nullable();
            $table->string('authorization_id')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_type')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_id')->nullable();
            $table->string('recurring_type')->nullable();
            $table->string('recurring_cycle')->nullable();
            $table->integer('recurring_cycle_count')->nullable();
            $table->text('invoice_url')->nullable();
            $table->json('invoice_items')->nullable();
            $table->json('customer_address')->nullable();
            $table->text('error_message')->nullable();
            $table->string('error_code')->nullable();
            $table->json('raw_response')->nullable();
            $table->json('webhook_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('payment_method_id')->references('id')->on('myfatoorah_payment_methods')->onDelete('set null');
            $table->index('invoice_id');
            $table->index('payment_id');
            $table->index('transaction_status');
            $table->index('customer_email');
            $table->index('is_recurring');
            $table->index('recurring_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('myfatoorah_payments');
    }
};

