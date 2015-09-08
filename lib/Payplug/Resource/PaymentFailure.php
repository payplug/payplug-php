<?php
namespace Payplug\Resource;

/**
 * Payment Failure information
 */
class PaymentFailure extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  PaymentFailure  The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PaymentFailure();
        $object->initialize($attributes);
        return $object;
    }
}
