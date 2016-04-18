<?php
namespace Payplug\Resource;

/**
 * A Notification
 */
class PaymentNotification extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array $attributes the default attributes.
     *
     * @return  PaymentNotification The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PaymentNotification();
        $object->initialize($attributes);
        return $object;
    }
}
