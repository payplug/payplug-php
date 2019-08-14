<?php
namespace Payplug\Core;
use Payplug;

/**
 * An authenticated HTTP client for PayPlug's API.
 *
 * @note This requires PHP's curl extension.
 */
class HttpClient
{
    /**
     * @var string  Constant that defines the path to cacert file which contains a set of trusted CA.
     */
    public static $CACERT_PATH;

    /**
     * @var null|IHttpRequest   set the request wrapper. For test purpose only.
     * You can set this to a mock of IHttpRequest, so that the request will not be performed.
     */
    public static $REQUEST_HANDLER = null;

    /**
     * @var array  Default User-Agent products made to improve the User-Agent HTTP header
     * sent for each HTTP request.
     */
    private static $defaultUserAgentProducts = array();

    /**
     * @var Payplug\Payplug The configuration for the HTTP Client. This configuration will be used to pass
     * the right token in the queries headers.
     */
    private $_configuration;

    /**
     * HttpClient constructor.
     *
     * @param   Payplug\Payplug    $authentication  the client configuration
     */
    public function __construct(Payplug\Payplug $authentication = null)
    {
        $this->_configuration = $authentication;
    }

    /**
     * Sends a POST request to the API.
     *
     * @param   string  $resource   the path to the remote resource
     * @param   array   $data       Request data
     * @param   bool    $authenticated  the request should be authenticated
     *
     * @return  array   the response in a dictionary with following keys:
     * <pre>
     *  'httpStatus'    => The 2xx HTTP status code as defined at http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2
     *  'httpResponse'  => The HTTP response
     * </pre>
     *
     * @throws  Payplug\Exception\UnexpectedAPIResponseException  When the API response is not parsable in JSON.
     * @throws  Payplug\Exception\HttpException                   When status code is not 2xx.
     * @throws  Payplug\Exception\ConnectionException             When an error was encountered while connecting to the resource.
     */
    public function post($resource, $data = null, $authenticated = true)
    {
        return $this->request('POST', $resource, $data, $authenticated);
    }

    /**
     * Sends a PATCH request to the API.
     *
     * @param   string  $resource   the path to the remote resource
     * @param   array   $data       Request data
     *
     * @return  array   the response in a dictionary with following keys:
     * <pre>
     *  'httpStatus'    => The 2xx HTTP status code as defined at http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2
     *  'httpResponse'  => The HTTP response
     * </pre>
     *
     * @throws  Payplug\Exception\UnexpectedAPIResponseException  When the API response is not parsable in JSON.
     * @throws  Payplug\Exception\HttpException                   When status code is not 2xx.
     * @throws  Payplug\Exception\ConnectionException             When an error was encountered while connecting to the resource.
     */
    public function patch($resource, $data = null)
    {
        return $this->request('PATCH', $resource, $data);
    }

    /**
     * Sends a DELETE request to the API.
     *
     * @param   string  $resource   the path to the remote resource
     * @param   array   $data       Request data
     *
     * @return  array   the response in a dictionary with following keys:
     * <pre>
     *  'httpStatus'    => The 2xx HTTP status code as defined at http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2
     *  'httpResponse'  => The HTTP response
     * </pre>
     *
     * @throws  Payplug\Exception\UnexpectedAPIResponseException  When the API response is not parsable in JSON.
     * @throws  Payplug\Exception\HttpException                   When status code is not 2xx.
     * @throws  Payplug\Exception\ConnectionException             When an error was encountered while connecting to the resource.
     */
    public function delete($resource, $data = null)
    {
        return $this->request('DELETE', $resource, $data);
    }

    /**
     * Sends a GET request to the API.
     *
     * @param   string  $resource       the path to the remote resource
     * @param   array   $data           Request data
     *
     * @return  array   the response in a dictionary with following keys:
     * <pre>
     *  'httpStatus'    => The 2xx HTTP status code {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2}
     *  'httpResponse'  => The HTTP response
     * </pre>
     *
     * @throws  Payplug\Exception\UnexpectedAPIResponseException  When the API response is not parsable in JSON.
     * @throws  Payplug\Exception\HttpException                   When status code is not 2xx.
     * @throws  Payplug\Exception\ConnectionException             When an error was encountered while connecting to the resource.
     */
    public function get($resource, $data = null)
    {
        return $this->request('GET', $resource, $data);
    }

    /**
     * Sends a test request to the remote API.
     *
     * @return  array   the response in a dictionary with following keys:
     * <pre>
     *  'httpStatus'    => The 2xx HTTP status code {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2}
     *  'httpResponse'  => The HTTP response
     * </pre>
     *
     * @throws  Payplug\Exception\UnexpectedAPIResponseException  When the API response is not parsable in JSON.
     * @throws  Payplug\Exception\HttpException                   When status code is not 2xx.
     * @throws  Payplug\Exception\ConnectionException             When an error was encountered while connecting to the resource.
     */
    public function testRemote() {
        return $this->request('GET', APIRoutes::getTestRoute(), null, false);
    }

    /**
     * Adds a default product for the User-Agent HTTP header sent for each HTTP request.
     *
     * @param   string  $product   the product's name
     * @param   string  $version   the product's version
     * @param   string  $comment   a comment about the product
     *
     */
    public static function addDefaultUserAgentProduct($product, $version = null, $comment = null)
    {
        self::$defaultUserAgentProducts[] = array($product, $version, $comment);
    }

    /**
     * Formats a product for a User-Agent HTTP header.
     *
     * @param   string  $product   the product name
     * @param   string  $version   (optional) product version
     * @param   string  $comment   (optional) comment about the product.
     *
     * @return  string  a formatted User-Agent string (`PRODUCT/VERSION (COMMENT)`)
     */
    private static function formatUserAgentProduct($product, $version = null, $comment = null)
    {
        $productString = $product;
        if ($version) {
            $productString .= '/' . $version;
        }
        if ($comment) {
            $productString .= ' (' . $comment . ')';
        }
        return $productString;
    }

    /**
     * Gets the User-Agent HTTP header sent for each HTTP request.
     */
    public static function getUserAgent()
    {
        $curlVersion = curl_version(); // Do not move this inside $headers even if it is used only there.
                                       // PHP < 5.4 doesn't support call()['value'] directly.
        $userAgent = self::formatUserAgentProduct('PayPlug-PHP',
                                                  Payplug\Core\Config::LIBRARY_VERSION,
                                                  sprintf('PHP/%s; curl/%s', phpversion(), $curlVersion['version']));
        foreach (self::$defaultUserAgentProducts as $product) {
            $userAgent .= ' ' . self::formatUserAgentProduct($product[0], $product[1], $product[2]);
        }
        return $userAgent;
    }

    /**
     * Performs a request.
     *
     * @param   string  $httpVerb       the HTTP verb (PUT, POST, GET, …)
     * @param   string  $resource       the path to the resource queried
     * @param   array   $data           the request content
     * @param   bool    $authenticated  the request should be authenticated
     *
     * @return array the response in a dictionary with following keys:
     * <pre>
     *  'httpStatus'    => The 2xx HTTP status code {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2}
     *  'httpResponse'  => The HTTP response
     * </pre>
     *
     * @throws  Payplug\Exception\UnexpectedAPIResponseException  When the API response is not parsable in JSON.
     * @throws  Payplug\Exception\HttpException                   When status code is not 2xx.
     * @throws  Payplug\Exception\ConnectionException             When an error was encountered while connecting to the resource.
     */
    private function request($httpVerb, $resource, array $data = null, $authenticated = true)
    {
        if (self::$REQUEST_HANDLER === null) {
            $request = new CurlRequest();
        }
        else {
            $request = self::$REQUEST_HANDLER;
        }

        $userAgent = self::getUserAgent();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: ' . $userAgent
        );
        if ($authenticated) {
            $headers[] = 'Authorization: Bearer ' . $this->_configuration->getToken();
            $headers[] = 'PayPlug-Version: ' . $this->_configuration->getApiVersion();
        }

        $request->setopt(CURLOPT_FAILONERROR, false);
        $request->setopt(CURLOPT_RETURNTRANSFER, true);
        $request->setopt(CURLOPT_CUSTOMREQUEST, $httpVerb);
        $request->setopt(CURLOPT_URL, $resource);
        $request->setopt(CURLOPT_HTTPHEADER, $headers);
        $request->setopt(CURLOPT_SSL_VERIFYPEER, true);
        $request->setopt(CURLOPT_SSL_VERIFYHOST, 2);
        $request->setopt(CURLOPT_CAINFO, self::$CACERT_PATH);
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

        // We want manage errors from HTTP in standards cases
        $curlStatusNotManage = array(
            0, // CURLE_OK
            22 // CURLE_HTTP_NOT_FOUND or CURLE_HTTP_RETURNED_ERROR
        );

        // If there was an HTTP error
        if (in_array($errorCode, $curlStatusNotManage) && substr($result['httpStatus'], 0, 1) !== '2') {
            $this->throwRequestException($result['httpResponse'], $result['httpStatus']);
        // Unreachable bracket marked as executable by old versions of XDebug
        } // If there was an error with curl
        elseif ($result['httpResponse'] === false || $errorCode) {
            $this->throwConnectionException($result['httpStatus'], $errorMessage);
        // Unreachable bracket marked as executable by old versions of XDebug
        }

        $result['httpResponse'] = json_decode($result['httpResponse'], true);

        if ($result['httpResponse'] === null) {
            throw new Payplug\Exception\UnexpectedAPIResponseException('API response is not valid JSON.', $result['httpResponse']);
        }

        return $result;
    }

    /**
     * Throws an exception from a given curl error.
     *
     * @param   int     $errorCode      the curl error code
     * @param   string  $errorMessage   the error message
     *
     * @throws  Payplug\Exception\ConnectionException
     */
    private function throwConnectionException($errorCode, $errorMessage)
    {
        throw new Payplug\Exception\ConnectionException(
            'Connection to the API failed with the following message: ' . $errorMessage, $errorCode
        );
    }

    /**
     * Throws an exception from a given HTTP response and status.
     *
     * @param   string  $httpResponse   the HTTP response
     * @param   int     $httpStatus     the HTTP status
     *
     * @throws  Payplug\Exception\HttpException   the generated exception from the request
     */
    private function throwRequestException($httpResponse, $httpStatus)
    {
        $exception = null;

        // Error 5XX
        if (substr($httpStatus, 0, 1) === '5') {
            throw new Payplug\Exception\PayplugServerException('Unexpected server error during the request.',
                $httpResponse, $httpStatus
            );
        }

        switch ($httpStatus) {
            case 400:
                throw new Payplug\Exception\BadRequestException('Bad request.', $httpResponse, $httpStatus);
                break;
            case 401:
                throw new Payplug\Exception\UnauthorizedException('Unauthorized. Please check your credentials.',
                    $httpResponse, $httpStatus);
                break;
            case 403:
                throw new Payplug\Exception\ForbiddenException('Forbidden error. You are not allowed to access this resource.',
                    $httpResponse, $httpStatus);
                break;
            case 404:
                throw new Payplug\Exception\NotFoundException('The resource you requested could not be found.',
                    $httpResponse, $httpStatus);
                break;
            case 405:
                throw new Payplug\Exception\NotAllowedException('The requested method is not supported by this resource.',
                    $httpResponse, $httpStatus);
                break;
        }

        throw new Payplug\Exception\HttpException('Unhandled HTTP error.', $httpResponse, $httpStatus);
    }
}

HttpClient::$CACERT_PATH = realpath(__DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/../../certs/cacert.pem'));
