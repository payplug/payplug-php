<?php
namespace Payplug\Resource;

/**
 * A Customer.
 */
class PaymentCustomer extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array $attributes the default attributes.
     *
     * @return  PaymentCustomer The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PaymentCustomer();
        $object->initialize($attributes);
        return $object;
    }
}
