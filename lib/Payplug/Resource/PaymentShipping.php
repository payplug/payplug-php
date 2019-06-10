<?php
namespace Payplug\Resource;

/**
 * A Payment Shipping.
 */
class PaymentShipping extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array $attributes the default attributes.
     *
     * @return  PaymentShipping The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PaymentShipping();
        $object->initialize($attributes);
        return $object;
    }
}
