<?php
namespace Payplug\Resource;

/**
 * An Authorization
 */
class PaymentAuthorization extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array $attributes the default attributes.
     *
     * @return  PaymentAuthorization The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PaymentAuthorization();
        $object->initialize($attributes);
        return $object;
    }
}
