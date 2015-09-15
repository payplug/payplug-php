<?php
namespace Payplug\Resource;

/**
 * A Credit Card.
 */
class Card extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  Card    The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new Card();
        $object->initialize($attributes);
        return $object;
    }
}
