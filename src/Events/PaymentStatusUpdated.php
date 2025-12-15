<?php

namespace Greelogix\MyFatoorah\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Greelogix\MyFatoorah\Models\MyFatoorahPayment;

class PaymentStatusUpdated
{
    use Dispatchable, SerializesModels;

    public MyFatoorahPayment $payment;
    public array $webhookData;

    public function __construct(MyFatoorahPayment $payment, array $webhookData = [])
    {
        $this->payment = $payment;
        $this->webhookData = $webhookData;
    }
}

