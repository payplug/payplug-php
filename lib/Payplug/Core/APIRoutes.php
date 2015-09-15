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

    // Payments routes
    const CREATE_PAYMENT    = '/payments';
    const RETRIEVE_PAYMENT  = '/payments/{PAYMENT_ID}';
    const LIST_PAYMENTS     = '/payments';

    // Refunds routes
    const CREATE_REFUND     = '/payments/{PAYMENT_ID}/refunds';
    const RETRIEVE_REFUND   = '/payments/{PAYMENT_ID}/refunds/{REFUND_ID}';
    const LIST_REFUNDS      = '/payments/{PAYMENT_ID}/refunds';

    /**
     * Get the route to a specified resource.
     *
     * @param   string  $route      One of the routes defined above
     * @param   array   $parameters The parameters required by the route.
     *
     * @return  string  the full URL to the resource
     */
    public static function getRoute($route, array $parameters = array(), array $pagination = array())
    {
        foreach ($parameters as $parameter => $value) {
            $route = str_replace('{' . $parameter . '}', $value, $route);
        }
        $query_pagination = '';
        if (!empty($pagination))
            $query_pagination = '?' . http_build_query($pagination);
        return self::$API_BASE_URL . '/v' . self::API_VERSION . $route . $query_pagination;
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
