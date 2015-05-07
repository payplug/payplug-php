<?php

/**
 * Payment Failure information
 */
class PayPlug_PaymentFailure extends PayPlug_APIResource
{
    /**
     * {@inheritdoc}
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PayPlug_PaymentFailure();
        $object->initialize($attributes);
        return $object;
    }
}