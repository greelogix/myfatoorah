<?php

namespace Greelogix\MyFatoorah\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyFatoorahPayment extends Model
{
    use SoftDeletes;

    protected $table = 'myfatoorah_payments';

    protected $fillable = [
        'invoice_id',
        'payment_id',
        'payment_method_id',
        'payment_method',
        'customer_name',
        'customer_email',
        'customer_mobile',
        'amount',
        'currency',
        'invoice_value',
        'invoice_display_value',
        'transaction_status',
        'transaction_date',
        'payment_gateway',
        'reference_id',
        'track_id',
        'authorization_id',
        'card_number',
        'card_type',
        'is_recurring',
        'recurring_id',
        'recurring_type',
        'recurring_cycle',
        'recurring_cycle_count',
        'invoice_url',
        'invoice_items',
        'customer_address',
        'error_message',
        'error_code',
        'raw_response',
        'webhook_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'invoice_value' => 'decimal:2',
        'invoice_display_value' => 'decimal:2',
        'transaction_date' => 'datetime',
        'is_recurring' => 'boolean',
        'invoice_items' => 'array',
        'customer_address' => 'array',
        'raw_response' => 'array',
        'webhook_data' => 'array',
        'recurring_cycle_count' => 'integer',
    ];

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return in_array($this->transaction_status, ['Paid', 'Success']);
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return in_array($this->transaction_status, ['Pending', 'InProgress']);
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return in_array($this->transaction_status, ['Failed', 'Error', 'Canceled']);
    }

    /**
     * Check if payment is recurring
     */
    public function isRecurring(): bool
    {
        return $this->is_recurring === true && !empty($this->recurring_id);
    }
}

