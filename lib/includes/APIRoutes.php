<?php

/**
 * Defines the routes to the PayPlug's API.
 */
class PayPlug_APIRoutes
{
//    const API_BASE_URL = 'https://api-dev.payplug.com';
    const API_BASE_URL = 'https://api-preprod.payplug.com';
//    const API_BASE_URL = 'https://api.payplug.com';
    const API_VERSION = 1;

    // Payments routes
    const CREATE_PAYMENT    = '/payments';
    const RETRIEVE_PAYMENT  = '/payments/{PAYMENT_ID}';

    // Refunds routes
    const CREATE_REFUND     = '/payments/{PAYMENT_ID}/refunds';
    const RETRIEVE_REFUND   = '/payments/{PAYMENT_ID}/refunds/{REFUND_ID}';
    const LIST_REFUNDS      = '/payments/{PAYMENT_ID}/refunds';

    // Test route
    public static $TEST;

    /**
     * @param string $route One of the routes defined above
     * @param array $parameters The parameters required by the route.
     * @return string the route to the resource
     */
    public static function getRoute($route, array $parameters = array())
    {
        foreach ($parameters as $parameter => $value) {
            $route = str_replace('{' . $parameter . '}', $value, $route);
        }
        return self::API_BASE_URL . '/v' . self::API_VERSION . $route;
    }
}
PayPlug_APIRoutes::$TEST = PayPlug_APIRoutes::API_BASE_URL . '/test';