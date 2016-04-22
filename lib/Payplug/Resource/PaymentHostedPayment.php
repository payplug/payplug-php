<?php
namespace Payplug\Resource;

/**
 * A Hosted Payment
 */
class PaymentHostedPayment extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  PaymentHostedPayment   The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PaymentHostedPayment();
        $object->initialize($attributes);
        return $object;
    }
}
