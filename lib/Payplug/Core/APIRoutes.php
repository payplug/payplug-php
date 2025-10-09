<?php

namespace Payplug\Core;

use Symfony\Component\Dotenv\Dotenv;

/**
 * Defines the routes to PayPlug's API.
 */
class APIRoutes
{
    /**
     * @var string  the root URL of the API.
     */
    public static $API_BASE_URL;
    public static $SERVICE_BASE_URL;

    const API_VERSION = 1;

    // Resources routes
    const PAYMENT_RESOURCE = '/payments';
    const REFUND_RESOURCE = '/payments/{PAYMENT_ID}/refunds';
    const KEY_RESOURCE = '/keys';
    const ACCOUNT_RESOURCE = '/account';
    const CARD_RESOURCE = '/cards';
    const INSTALLMENT_PLAN_RESOURCE = '/installment_plans';
    const ONEY_PAYMENT_SIM_RESOURCE = '/oney_payment_simulations';
    const ACCOUNTING_REPORT_RESOURCE = '/accounting_reports';
    const PUBLISHABLE_KEYS = '/publishable_keys';
    const OAUTH2_TOKEN_RESOURCE = '/oauth2/token';
    const OAUTH2_AUTH_RESOURCE = '/oauth2/auth';
    const CLIENT_RESOURCE = '/users/api/v1/clients';

    // Service route
    const TELEMETRY_SERVICE = '/merchant-plugin-data-collectors/api/v1/plugin_telemetry';
    const PLUGIN_SETUP_SERVICE = '/users/api/v1/plugin_setup';
    const USER_SERVICE = '/users';


    /**
     * Get the route to a specified resource.
     *
     * @param string $route One of the routes defined above
     * @param string $resourceId The resource id you want to get. If null, will point to the endpoint.
     * @param array $parameters The parameters required by the route.
     * @param array $query_datas The query parameters add to the route.
     *
     * @return  string  the full URL to the resource
     */
    public static function getRoute($route, $resourceId = null, array $parameters = array(), array $query_datas = array(), $with_version = true)
    {
        foreach ($parameters as $parameter => $value) {
            $route = str_replace('{' . $parameter . '}', $value, $route);
        }

        $resourceIdUrl = $resourceId ? '/' . $resourceId : '';

        $query_parameters = '';
        if (!empty($query_datas))
            $query_parameters = '?' . http_build_query($query_datas);

        if (in_array($route, [self::OAUTH2_TOKEN_RESOURCE, self::OAUTH2_AUTH_RESOURCE]) && false !== strpos(self::$API_BASE_URL, 'https://service.')) {
            self::$API_BASE_URL = 'https://hydra--4444.external.gamma.notpayplug.com';
        }

        return self::$API_BASE_URL . ($with_version ? '/v' . self::API_VERSION : '') . $route . $resourceIdUrl . $query_parameters;
    }

    /**
     * Get the route to a specified resource.
     *
     * @param string $route One of the routes defined above
     * @param array $parameters The parameters required by the route.
     *
     * @return  string  the full URL to the resource
     *
     */
    public static function getServiceRoute($route, array $parameters = array())
    {
        return self::$SERVICE_BASE_URL . $route . ($parameters ? '?' . http_build_query($parameters) : '');
    }

    /**
     * @description set $API_BASE_URL from plugin
     * @param $apiBaseUrl
     */
    public static function setApiBaseUrl($apiBaseUrl)
    {
        self::$API_BASE_URL = $apiBaseUrl;
    }

    /**
     * @description set $SERVICE_BASE_URL from plugin
     * @param $serviceBaseUrl
     */
    public static function setServiceBaseUrl($serviceBaseUrl)
    {
        self::$SERVICE_BASE_URL = $serviceBaseUrl;
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
APIRoutes::$SERVICE_BASE_URL = 'https://retail.service.payplug.com';
