<?php
namespace Payplug\Resource;

/**
 * A Notification
 */
class Notification extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  Notification   The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new Notification();
        $object->initialize($attributes);
        return $object;
    }
}
