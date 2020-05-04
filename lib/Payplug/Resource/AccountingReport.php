<?php
namespace Payplug\Resource;
use Payplug;

/**
 * An accounting report
 */
class AccountingReport extends APIResource implements IVerifiableAPIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  AccountingReport The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new AccountingReport();
        $object->initialize($attributes);
        return $object;
    }

    /**
     * Retrieves an AccountingReport.
     *
     * @param   string             $reportId  the accounting report ID
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  null|AccountingReport the retrieved report or null on error
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function retrieve($reportId, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->get(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::ACCOUNTING_REPORT_RESOURCE, $reportId)
        );

        return AccountingReport::fromAttributes($response['httpResponse']);
    }

    /**
     * Creates an AccountingReport.
     *
     * @param   array               $data       API data for accounting report creation
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  null|AccountingReport the created accounting report instance
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function create(array $data, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->post(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::ACCOUNTING_REPORT_RESOURCE),
            $data
        );

        return AccountingReport::fromAttributes($response['httpResponse']);
    }

    /**
     * Returns an API resource that you can trust.
     *
     * @param   Payplug\Payplug $payplug the client configuration.
     *
     * @return  Payplug\Resource\APIResource The consistent API resource.
     *
     * @throws  Payplug\Exception\UndefinedAttributeException when the local resource is invalid.
     */
    function getConsistentResource(Payplug\Payplug $payplug = null)
    {
        if (!array_key_exists('id', $this->_attributes)) {
            throw new Payplug\Exception\UndefinedAttributeException('The id of the accounting report is not set.');
        }

        return AccountingReport::retrieve($this->_attributes['id'], $payplug);
    }
}
