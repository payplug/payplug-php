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
     * Send data to Merchant Plugins Data Collector Micro Service
     *
     * @param string $data
     * @param \Payplug\Payplug|null $payplug
     * @return array
     * @throws Exception\ConnectionException
     * @throws Exception\HttpException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public static function send(string $data,  Payplug\Payplug $payplug = null) {

        $data = json_decode($data,true);
        $httpClient = new Payplug\Core\HttpClient();

        return $response = $httpClient->post(
            Payplug\Core\APIRoutes::$MERCHANT_PLUGINS_DATA_COLLECTOR_RESOURCE,
            $data,
            false
        );

    }
    /**
     * this is a mock for send function
     *
     * @param string $data
     * @param \Payplug\Payplug|null $payplug
     * @return array
     * @throws Exception\UnprocessableEntityException
     */
    public static function mockSend(string $data, Payplug\Payplug $payplug = null)
    {
        // Simulate API response
        $data = json_decode($data, true);

        self::validateData($data);

        $responseData = array(
            'id' => '64de13f259a577c644d0fb61',
            'data' => $data
        );
        return array(
            'httpStatus' => 201,
            'httpResponse' => json_encode(
                $responseData
            )
        );

    }

    /**
     * Validate the $data and return UnprocessableEntityException
     *
     * @param array $data
     * @throws Exception\UnprocessableEntityException
     */
    public static function validateData(array $data)
    {
        $requiredFields = array('version', 'php_version', 'name', 'source', 'domains', 'configurations', 'modules');

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new Payplug\Exception\UnprocessableEntityException(
                    'The server encountered an error while processing the request. The submitted data could not be processed.',
                    '{"detail":[{"loc":["body","' . $field . '"],"msg":"field required","type":"value_error.missing"}]}',
                    422
                );
            }
        }
        if (!is_array($data['domains']) || empty($data['domains']) || !isset($data['domains'][0]['url']) || !isset($data['domains'][0]['default'])) {
            throw new Payplug\Exception\UnprocessableEntityException(
                'The server encountered an error while processing the request. The submitted data could not be processed.',
                '{"detail":[{"loc":["body","domains"],"msg":"invalid structure","type":"value_error.invalid_structure"}]}',
                422
            );
        }

        if (!is_array($data['configurations']) || empty($data['configurations']) || !isset($data['configurations']['name'])) {
            throw new Payplug\Exception\UnprocessableEntityException(
                'The server encountered an error while processing the request. The submitted data could not be processed.',
                '{"detail":[{"loc":["body","configurations"],"msg":"invalid structure","type":"value_error.invalid_structure"}]}',
                422
            );
        }

        if (!is_array($data['modules']) || empty($data['modules']) || !isset($data['modules'][0]['name']) || !isset($data['modules'][0]['version'])) {
            throw new Payplug\Exception\UnprocessableEntityException(
                'The server encountered an error while processing the request. The submitted data could not be processed.',
                '{"detail":[{"loc":["body","modules"],"msg":"invalid structure","type":"value_error.invalid_structure"}]}',
                422
            );
        }



    }
}