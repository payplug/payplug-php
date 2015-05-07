<?php

/**
 * A Credit Card.
 */
class PayPlug_Card extends PayPlug_APIResource
{
    /**
     * {@inheritdoc}
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PayPlug_Card();
        $object->initialize($attributes);
        return $object;
    }
}