<?php

/**
 * A Hosted Payment
 */
class PayPlug_HostedPayment extends PayPlug_APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  PayPlug_HostedPayment   The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PayPlug_HostedPayment();
        $object->initialize($attributes);
        return $object;
    }
}