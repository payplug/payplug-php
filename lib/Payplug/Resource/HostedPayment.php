<?php
namespace Payplug\Resource;

/**
 * A Hosted Payment
 */
class HostedPayment extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  HostedPayment   The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new HostedPayment();
        $object->initialize($attributes);
        return $object;
    }
}
