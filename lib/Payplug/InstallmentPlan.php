<?php
namespace Payplug;

/**
 * The InstallmentPlan DAO simplifies the access to most useful methods
 **/
class InstallmentPlan
{
	/**
     * Retrieves an InstallmentPlan.
     *
     * @param   string          $installmentPlanId      the installment plan ID
     * @param   Payplug         $payplug                the client configuration
     *
     * @return  null|Resource\InstallmentPlan the retrieved installment plan or null on error
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function retrieve($installmentPlanId, Payplug $payplug = null)
    {
        return Resource\InstallmentPlan::retrieve($installmentPlanId, $payplug);
    }

	/**
     * Aborts an InstallmentPlan.
     *
     * @param   string          $installmentPlanId      the installment plan ID
     * @param   Payplug         $payplug                the client configuration
     *
     * @return  null|Resource\InstallmentPlan the aborted installment plan or null on error
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function abort($installmentPlanId, Payplug $payplug = null)
    {
        $installmentPlan = Resource\InstallmentPlan::fromAttributes(array('id' => $installmentPlanId));
        return $installmentPlan->abort($payplug);
    }

    /**
     * Creates an InstallmentPlan.
     *
     * @param   array           $data           API data for payment creation
     * @param   Payplug         $payplug        the client configuration
     *
     * @return  null|Resource\InstallmentPlan   the created payment instance
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function create(array $data, Payplug $payplug = null)
    {
    	return Resource\InstallmentPlan::create($data, $payplug);
    }
};
