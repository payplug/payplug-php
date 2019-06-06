<?php
namespace Payplug\Resource;

/**
 * A Customer.
 */
class PaymentBilling extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array $attributes the default attributes.
     *
     * @return  PaymentBilling The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PaymentBilling();
        $object->initialize($attributes);
        return $object;
    }
}
