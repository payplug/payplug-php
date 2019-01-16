<?php
namespace Payplug\Resource;

/**
 * An installment plan schedule
 */
class InstallmentPlanSchedule extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  InstallmentPlanSchedule   The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new InstallmentPlanSchedule();
        $object->initialize($attributes);
        return $object;
    }
}
