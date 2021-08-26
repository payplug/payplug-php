<?php
namespace Payplug\Core;

/**
 * Defines the routes to PayPlug's API.
 */
class APIRoutes
{
    /**
     * @var string  the root URL of the API.
     */
    public static $API_BASE_URL;

    const API_VERSION = 1;

    // Resources routes
    const PAYMENT_RESOURCE           = '/payments';
    const REFUND_RESOURCE            = '/payments/{PAYMENT_ID}/refunds';
    const KEY_RESOURCE               = '/keys';
    const ACCOUNT_RESOURCE           = '/account';
    const CARD_RESOURCE              = '/cards';
    const INSTALLMENT_PLAN_RESOURCE  = '/installment_plans';
    const ONEY_PAYMENT_SIM_RESOURCE  = '/oney_payment_simulations';
    const ACCOUNTING_REPORT_RESOURCE = '/accounting_reports';


    /**
     * Get the route to a specified resource.
     *
     * @param   string $route One of the routes defined above
     * @param   string $resourceId The resource id you want to get. If null, will point to the endpoint.
     * @param   array $parameters The parameters required by the route.
     * @param   array $pagination The pagination parameters (mainly page and per_page keys that will be appended to the
     *                            query parameters of the request.
     *
     * @return  string  the full URL to the resource
     */
    public static function getRoute($route, $resourceId = null, array $parameters = array(), array $pagination = array())
    {
        foreach ($parameters as $parameter => $value) {
            $route = str_replace('{' . $parameter . '}', $value, $route);
        }

        $resourceIdUrl = $resourceId ? '/' . $resourceId : '';

        $query_pagination = '';
        if (!empty($pagination))
            $query_pagination = '?' . http_build_query($pagination);

        return self::$API_BASE_URL . '/v' . self::API_VERSION . $route . $resourceIdUrl . $query_pagination;
    }

    /**
     * Gets a route that allows to check whether the remote API is up.
     *
     * @return  string  the full URL to the test resource
     */
    public static function getTestRoute()
    {
        return APIRoutes::$API_BASE_URL . '/test';
    }
}

APIRoutes::$API_BASE_URL = 'https://api.payplug.com';
