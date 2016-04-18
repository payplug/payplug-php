<?php
namespace Payplug\Resource;

/**
 * A Credit Card.
 */
class PaymentCard extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array $attributes the default attributes.
     *
     * @return  PaymentCard The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PaymentCard();
        $object->initialize($attributes);
        return $object;
    }
}
