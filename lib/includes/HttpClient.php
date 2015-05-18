<?php

/**
 * An authenticated HTTP client for PayPlug's API.
 * @note This requires PHP's curl extension.
 */
class PayPlug_HttpClient
{
    const VERSION = '1.0.0';
    private $_configuration;

    /**
     * @param PayPlug_ClientConfiguration $configuration the client configuration
     */
    public function __construct(PayPlug_ClientConfiguration $configuration)
    {
        $this->_configuration = $configuration;
    }

    /**
     * A GET request to the API
     * @param string $resource the path to the remote resource
     * @param array $data Request data
     * @return array the response in a dictionary with keys 'httpStatus' and 'httpResponse'.
     */
    public function post($resource, $data = null)
    {
        return $this->request('POST', $resource, $data);
    }

    /**
     * A GET request to the API
     * @param string $resource the path to the remote resource
     * @param array $data Request data
     * @return array the response in a dictionary with keys 'httpStatus' and 'httpResponse'.
     */
    public function get($resource, $data = null)
    {
        return $this->request('GET', $resource, $data);
    }

    /**
     * Perform a request
     * @param string $httpVerb the HTTP verb (PUT, POST, GET, …)
     * @param string $resource the path to the resource queried
     * @param array $data request content
     * @return array the response in a dictionary with keys 'httpStatus' and 'httpResponse'.
     * @throws PayPlug_HttpException
     */
    private function request($httpVerb, $resource, array $data = null)
    {
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: PayPlug PHP Client ' . PayPlug_HttpClient::VERSION,
            'X-PHP-Version: ' . phpversion(),
            'Authorization: Bearer ' . $this->_configuration->getToken()
        );

        $curl = curl_init();
        // curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $httpVerb);
        curl_setopt($curl, CURLOPT_URL, $resource);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSLVERSION,
            defined('CURL_SSLVERSION_TLSv1_2') ? CURL_SSLVERSION_TLSv1_2 : CURL_SSLVERSION_TLSv1
        );
        curl_setopt($curl, CURLOPT_CAINFO, dirname(dirname(__FILE__)) . '/certs/cacert.pem');
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $result = array(
            'httpResponse'  => curl_exec($curl),
            'httpStatus'    => curl_getinfo($curl, CURLINFO_HTTP_CODE)
        );

        // If there was an error
        if (substr($result['httpStatus'], 0, 1) !== '2') {
            throw $this->getRequestException($result['httpResponse'], $result['httpStatus']);
        }

        $result['httpResponse'] = json_decode($result['httpResponse'], true);

        curl_close($curl);

        return $result;
    }

    /**
     * Generates an exception from a given HTTP response and status.
     * @param string $httpResponse the HTTP response
     * @param int $httpStatus the HTTP status
     * @return PayPlug_HttpException the generated exception from the request
     */
    private function getRequestException($httpResponse, $httpStatus)
    {
        $exception = null;

        // Error 5XX
        if (substr($httpStatus, 0, 1) === '5') {
            return new PayPlug_PayPlugServerException('Unexpected server error during the request.',
                $httpResponse,
                $httpStatus
            );
        }

        switch ($httpStatus) {
            case 400:
                return new PayPlug_BadRequest('Bad request.', $httpResponse, $httpStatus);
                break;
            case 401:
                return new PayPlug_Unauthorized('Unauthorized. Please check your credentials.',
                    $httpResponse, $httpStatus);
                break;
            case 403:
                return new PayPlug_Forbidden('Forbidden error. You are not allowed to access this resource.',
                    $httpResponse, $httpStatus);
                break;
            case 404:
                return new PayPlug_NotFound('The resource you requested could not be found.',
                    $httpResponse, $httpStatus);
                break;
            case 405:
                return new PayPlug_NotAllowed('The requested method is not supported by this resource.',
                    $httpResponse, $httpStatus);
                break;
        }

        return new PayPlug_HttpException('Unhandled HTTP error.', $httpResponse, $httpStatus);
    }
}