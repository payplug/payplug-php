<?php

/**
 * Payment Failure information
 */
class PayPlug_PaymentFailure extends PayPlug_APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  PayPlug_APIResource The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PayPlug_PaymentFailure();
        $object->initialize($attributes);
        return $object;
    }
}