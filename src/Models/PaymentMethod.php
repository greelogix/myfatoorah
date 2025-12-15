<?php

namespace Greelogix\MyFatoorah\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;

    protected $table = 'myfatoorah_payment_methods';

    protected $fillable = [
        'payment_method_id',
        'payment_method_en',
        'payment_method_ar',
        'payment_method_code',
        'is_direct_payment',
        'service_charge',
        'total_amount',
        'currency_iso',
        'image_url',
        'is_active',
        'is_active_ios',
        'is_active_android',
        'is_active_web',
        'sort_order',
        'min_invoice_value',
        'max_invoice_value',
        'api_payment_method_id',
        'extra_fees',
        'supported_currencies',
    ];

    protected $casts = [
        'is_direct_payment' => 'boolean',
        'is_active' => 'boolean',
        'is_active_ios' => 'boolean',
        'is_active_android' => 'boolean',
        'is_active_web' => 'boolean',
        'service_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'min_invoice_value' => 'decimal:2',
        'max_invoice_value' => 'decimal:2',
        'extra_fees' => 'array',
        'supported_currencies' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Get payments for this payment method
     */
    public function payments()
    {
        return $this->hasMany(MyFatoorahPayment::class, 'payment_method_id');
    }

    /**
     * Scope for active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for active payment methods by platform
     */
    public function scopeActiveForPlatform($query, string $platform)
    {
        $column = match(strtolower($platform)) {
            'ios' => 'is_active_ios',
            'android' => 'is_active_android',
            'web' => 'is_active_web',
            default => 'is_active',
        };

        return $query->where('is_active', true)->where($column, true);
    }

    /**
     * Scope for direct payment methods
     */
    public function scopeDirectPayment($query)
    {
        return $query->where('is_direct_payment', true);
    }
}

