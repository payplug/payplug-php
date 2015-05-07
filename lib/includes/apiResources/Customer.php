<?php

/**
 * A Customer.
 */
class PayPlug_Customer extends PayPlug_APIResource
{
    /**
     * {@inheritdoc}
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PayPlug_Customer();
        $object->initialize($attributes);
        return $object;
    }
}