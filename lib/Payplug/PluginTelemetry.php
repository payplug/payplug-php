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


    public static function mockSend(array $data, Payplug\Payplug $payplug = null)
    {
        // Simulate API response
        if (!isset($data['version']) || !isset($data['php_version'])) {
            throw new Payplug\Exception\UnprocessableEntityException('The server encountered an error while processing the request. The submitted data could not be processed.',
                                                                     '{"detail":[{"loc":["body","version"],"msg":"field required","type":"value_error.missing"}]}',422);
        } else {
            return array(
                'httpStatus' => 201,
                'httpResponse' => json_encode(
                    array(
                        'id' => '64de13f259a577c644d0fb61',
                        'version' => $data['version'],
                        'php_version' => $data['php_version']
                    )
                )
            );
        }
    }

}