<?php
namespace Payplug;

/**
 * The Accoiunting report DAO simplifies the access to most useful methods
 **/
class AccountingReport
{
    /**
     * Retrieves an accounting report.
     *
     * @param   string                      $reportId      the accounting report ID
     * @param   Payplug $payplug  the client configuration
     *
     * @return  null|Resource\AccountingReport the retrieved report or null on error
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function retrieve($reportId, Payplug $payplug = null)
    {
        return Resource\AccountingReport::retrieve($reportId, $payplug);
    }

    /**
     * Creates an accounting report.
     *
     * @param   array                       $data           API data for accounting report creation
     * @param   Payplug $payplug  the client configuration
     *
     * @return  null|Resource\AccountingReport the created accounting report instance
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function create(array $data, Payplug $payplug = null)
    {
        return Resource\AccountingReport::create($data, $payplug);
    }
};
