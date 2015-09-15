<?php
namespace Payplug\Resource;

/**
 * A Customer.
 */
class Customer extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  Customer    The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new Customer();
        $object->initialize($attributes);
        return $object;
    }
}
