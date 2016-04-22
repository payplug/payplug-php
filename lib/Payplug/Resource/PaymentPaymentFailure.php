<?php
namespace Payplug\Resource;

/**
 * Payment Failure information
 */
class PaymentPaymentFailure extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array $attributes the default attributes.
     *
     * @return  PaymentPaymentFailure  The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PaymentPaymentFailure();
        $object->initialize($attributes);
        return $object;
    }
}
