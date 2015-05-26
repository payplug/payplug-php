<?php

/**
 * A Credit Card.
 */
class PayPlug_Card extends PayPlug_APIResource
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
        $object = new PayPlug_Card();
        $object->initialize($attributes);
        return $object;
    }
}