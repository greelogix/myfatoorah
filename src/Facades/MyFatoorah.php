<?php

namespace Greelogix\MyFatoorah\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Greelogix\MyFatoorah\Services\MyFatoorahService initiatePayment(array $data)
 * @method static \Greelogix\MyFatoorah\Services\MyFatoorahService executePayment(string $paymentId, array $data = [])
 * @method static \Greelogix\MyFatoorah\Services\MyFatoorahService getPaymentStatus(string $paymentId)
 * @method static \Greelogix\MyFatoorah\Services\MyFatoorahService getPaymentMethods()
 * @method static \Greelogix\MyFatoorah\Services\MyFatoorahService sendPayment(array $data)
 * @method static \Greelogix\MyFatoorah\Services\MyFatoorahService getInvoiceStatus(string $invoiceId)
 * @method static \Greelogix\MyFatoorah\Services\MyFatoorahService cancelInvoice(string $invoiceId)
 * @method static \Greelogix\MyFatoorah\Services\MyFatoorahService createRecurringPayment(array $data)
 * @method static \Greelogix\MyFatoorah\Services\MyFatoorahService executeRecurringPayment(string $recurringId, array $data)
 *
 * @see \Greelogix\MyFatoorah\Services\MyFatoorahService
 */
class MyFatoorah extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'myfatoorah';
    }
}

