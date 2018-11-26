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
        $installment_plan = Resource\InstallmentPlan::fromAttributes(array('id' => $installmentPlanId));
    	return $installment_plan->abort($payplug);
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

    /**
     * List installment plans.
     *
     * @param   int                 $perPage    number of results per page
     * @param   int                 $page       the page number
     * @param   Payplug             $payplug    the client configuration
     * 
     * @return  null|Resource\InstallmentPlan[]   the array of installment plans
     *
     * @throws  Exception\InvalidInstallmentPlanException
     * @throws  Exception\UnexpectedAPIResponseException
     */
    public static function listInstallmentPlans($perPage = null, $page = null, Payplug $payplug = null)
    {
        return Resource\InstallmentPlan::listInstallmentPlans($perPage, $page, $payplug);
    }
};