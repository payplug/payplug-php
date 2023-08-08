<?php


namespace Payplug;
use Payplug;


/**
 * sent Data to Merchant Plugins Data Collector
 * Class PluginTelemetry
 * @package Payplug
 */
class PluginTelemetry
{

    /**
     *
     * Send data to Merchant Plugins Data Collector Micro Service
     *
     * @param array $data
     * @param \Payplug\Payplug|null $payplug
     * @return array|false|Core\CurlRequest|Core\IHttpRequest|null
     * @throws Exception\ConnectionException
     * @throws Exception\HttpException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public static function send(array $data,  Payplug\Payplug $payplug = null) {

        $httpClient = new Payplug\Core\HttpClient();

        return $response = $httpClient->post(
            Payplug\Core\APIRoutes::$MERCHANT_PLUGINS_DATA_COLLECTOR_RESOURCE,
            $data,
            false
        );

    }
}