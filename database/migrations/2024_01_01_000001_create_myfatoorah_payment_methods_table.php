<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('myfatoorah_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_method_id')->unique();
            $table->string('payment_method_en')->nullable();
            $table->string('payment_method_ar')->nullable();
            $table->string('payment_method_code')->nullable();
            $table->boolean('is_direct_payment')->default(false);
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('currency_iso', 3)->default('KWD');
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_active_ios')->default(true);
            $table->boolean('is_active_android')->default(true);
            $table->boolean('is_active_web')->default(true);
            $table->integer('sort_order')->default(0);
            $table->decimal('min_invoice_value', 10, 2)->nullable();
            $table->decimal('max_invoice_value', 10, 2)->nullable();
            $table->integer('api_payment_method_id')->nullable();
            $table->json('extra_fees')->nullable();
            $table->json('supported_currencies')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('payment_method_id');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('myfatoorah_payment_methods');
    }
};

