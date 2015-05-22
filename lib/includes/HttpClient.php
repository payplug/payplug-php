<?php

/**
 * Generified HttpRequest so that it can easily be mocked
 */
interface PayPlug_IHttpRequest
{
    /**
     * Simple wrapper for curl_setopt
     * @link http://php.net/manual/en/function.curl-setopt.php
     */
    function setopt($option, $value);

    /**
     * Simple wrapper for curl_exec
     * @link http://php.net/manual/en/function.curl-exec.php
     */
    function exec();

    /**
     * Simple wrapper for curl_getinfo
     * @link http://php.net/manual/en/function.curl-getinfo.php
     */
    function getinfo($option);

    /**
     * Simple wrapper for curl_close
     * @link http://php.net/manual/en/function.curl-close.php
     */
    function close();

    /**
     * Simple wrapper for curl_error
     * @link http://php.net/manual/en/function.curl-close.php
     */
    function error();

    /**
     * Simple wrapper for curl_errno
     * @link http://php.net/manual/en/function.curl-close.php
     */
    function errno();
}

/**
 * Implementation of {@link PayPlug_IHttpRequest} that uses curl
 */
class PayPlug_CurlRequest implements PayPlug_IHttpRequest
{
    private $_curl;

    /**
     * PayPlug_CurlRequest constructor
     * Initializes a curl request
     */
    public function __construct()
    {
        $this->_curl = curl_init();
    }

    /**
     * {@inheritdoc}
     */
    public function setopt($option, $value)
    {
        return curl_setopt($this->_curl, $option, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getinfo($option)
    {
        return curl_getinfo($this->_curl, $option);
    }

    /**
     * {@inheritdoc}
     */
    public function exec()
    {
        return curl_exec($this->_curl);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        curl_close($this->_curl);
    }

    /**
     * {@inheritdoc}
     */
    public function error()
    {
        return curl_error($this->_curl);
    }

    /**
     * {@inheritdoc}
     */
    public function errno()
    {
        return curl_errno($this->_curl);
    }
}

/**
 * An authenticated HTTP client for PayPlug's API.
 * @note This requires PHP's curl extension.
 */
class PayPlug_HttpClient
{
    const VERSION = '1.0.0';
    /**
     * @var null|PayPlug_IHttpRequest set the request wrapper. For test purpose only.
     * You can set this to a mock of PayPlug_IHttpRequest, so that the request will not be performed.
     */
    public static $REQUEST_HANDLER = null;

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
     * @throws PayPlug_HttpException
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
     * @throws PayPlug_HttpException
     */
    public function get($resource, $data = null)
    {
        return $this->request('GET', $resource, $data);
    }

    /**
     * Sends a test request to the remote API.
     * @return array the response in a dictionary with keys 'httpStatus' and 'httpResponse'.
     * @throws PayPlug_HttpException
     * @throws PayPlug_UnexpectedAPIResponseException
     */
    public function testRemote() {
        return $this->request('GET', PayPlug_APIRoutes::$TEST, null, false);
    }

    /**
     * Perform a request
     * @param string $httpVerb the HTTP verb (PUT, POST, GET, …)
     * @param string $resource the path to the resource queried
     * @param array $data request content
     * @param bool $authenticated the request should be authenticated
     * @return array the response in a dictionary with keys 'httpStatus' and 'httpResponse'.
     * @throws PayPlug_UnexpectedAPIResponseException
     */
    private function request($httpVerb, $resource, array $data = null, $authenticated = true)
    {
        if (self::$REQUEST_HANDLER === null) {
            $request = new PayPlug_CurlRequest();
        }
        else {
            $request = self::$REQUEST_HANDLER;
        }

        $curl_version = curl_version(); // Do not move this inside $headers even if it is used only there.
                                        // PHP < 5.4 doesn't support call()['value'] directly.
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: PayPlug PHP Client ' . PayPlug_HttpClient::VERSION,
            'X-PHP-Version: ' . phpversion(),
            'X-Curl-Version: ' . $curl_version['version']
        );
        if ($authenticated) {
            $headers[] = 'Authorization: Bearer ' . $this->_configuration->getToken();
        }

//        $request->setopt(CURLOPT_VERBOSE, true);
        $request->setopt(CURLOPT_FAILONERROR, true);
        $request->setopt(CURLOPT_RETURNTRANSFER, true);
        $request->setopt(CURLOPT_CUSTOMREQUEST, $httpVerb);
        $request->setopt(CURLOPT_URL, $resource);
        $request->setopt(CURLOPT_HTTPHEADER, $headers);
        $request->setopt(CURLOPT_SSL_VERIFYPEER, true);
        $request->setopt(CURLOPT_SSL_VERIFYHOST, 2);
        $request->setopt(CURLOPT_CAINFO, realpath(dirname(__FILE__) . '/../certs/cacert.pem'));
//        $request->setopt(CURLOPT_SSLVERSION,
//            defined('CURL_SSLVERSION_TLSv1_2') ? CURL_SSLVERSION_TLSv1_2 : CURL_SSLVERSION_DEFAULT
//        );
        if (!empty($data)) {
            $request->setopt(CURLOPT_POSTFIELDS, json_encode($data));
        }

        $result = array(
            'httpResponse'  => $request->exec(),
            'httpStatus'    => $request->getInfo(CURLINFO_HTTP_CODE)
        );

        // We must do this before closing curl
        $errorCode = $request->errno();
        $errorMessage = $request->error();

        $request->close();

        // If there was an error with curl
        if ($result['httpResponse'] === false || $errorCode) {
            $this->throwConnectionException($errorCode, $errorMessage);
        //@codeCoverageIgnoreStart
        // Unreachable bracket marked as executable by old versions of XDebug
        } // If there was an HTTP error
        //@codeCoverageIgnoreEnd
        elseif (substr($result['httpStatus'], 0, 1) !== '2') {
            $this->throwRequestException($result['httpResponse'], $result['httpStatus']);
        //@codeCoverageIgnoreStart
        // Unreachable bracket marked as executable by old versions of XDebug
        }
        //@codeCoverageIgnoreEnd

        $result['httpResponse'] = json_decode($result['httpResponse'], true);

        if ($result['httpResponse'] === null) {
            throw new PayPlug_UnexpectedAPIResponseException('API response is not valid JSON.', $result['httpResponse']);
        }

        return $result;
    }

    /**
     * Throw an exception from a given curl error
     * @param int $errorCode the curl error code
     * @param string $errorMessage the error message
     * @throws PayPlug_ConnectionException
     */
    private function throwConnectionException($errorCode, $errorMessage)
    {
        throw new PayPlug_ConnectionException(
            'Connection to the API failed with the following message: ' . $errorMessage, $errorCode
        );
    }

    /**
     * Throws an exception from a given HTTP response and status.
     * @param string $httpResponse the HTTP response
     * @param int $httpStatus the HTTP status
     * @throws PayPlug_HttpException the generated exception from the request
     */
    private function throwRequestException($httpResponse, $httpStatus)
    {
        $exception = null;

        // Error 5XX
        if (substr($httpStatus, 0, 1) === '5') {
            throw new PayPlug_PayPlugServerException('Unexpected server error during the request.',
                $httpResponse,
                $httpStatus
            );
        }

        switch ($httpStatus) {
            case 400:
                throw new PayPlug_BadRequestException('Bad request.', $httpResponse, $httpStatus);
                break;
            case 401:
                throw new PayPlug_UnauthorizedException('Unauthorized. Please check your credentials.',
                    $httpResponse, $httpStatus);
                break;
            case 403:
                throw new PayPlug_ForbiddenException('Forbidden error. You are not allowed to access this resource.',
                    $httpResponse, $httpStatus);
                break;
            case 404:
                throw new PayPlug_NotFoundException('The resource you requested could not be found.',
                    $httpResponse, $httpStatus);
                break;
            case 405:
                throw new PayPlug_NotAllowedException('The requested method is not supported by this resource.',
                    $httpResponse, $httpStatus);
                break;
        }

        throw new PayPlug_HttpException('Unhandled HTTP error.', $httpResponse, $httpStatus);
    }
}