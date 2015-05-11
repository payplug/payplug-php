<?php

/**
 * A Hosted Payment
 */
class PayPlug_HostedPayment extends PayPlug_APIResource
{
    /**
     * {@inheritdoc}
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PayPlug_HostedPayment();
        $object->initialize($attributes);
        return $object;
    }
}