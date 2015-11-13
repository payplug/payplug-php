<?php
namespace Payplug\Exception;

/**
 * HTTP errors
 */
class HttpException extends PayplugException
{
    /**
     * @var null|string The plain HTTP response.
     */
    private $_httpResponse;

    /**
     * HttpException constructor.
     *
     * @param   string  $message        the exception message
     * @param   string  $httpResponse   the http response content
     * @param   int     $code           the exception code
     */
    public function __construct($message, $httpResponse = null, $code = 0)
    {
        $this->_httpResponse = $httpResponse;
        parent::__construct($message, $code);
    }

    /**
     * {@inheritdoc} It also appends the HTTP response to the returned string.
     */
    public function __toString()
    {
        return parent::__toString() . "; HTTP Response: {$this->_httpResponse}";
    }

    /**
     * Get the plain HTTP response which caused the exception.
     *
     * @return  string  the HTTP response
     */
    public function getHttpResponse()
    {
        return $this->_httpResponse;
    }

    /**
     * Try to parse the HTTP response as a JSON array and return it.
     *
     * @return  array|null  the error array if the HTTP response was a properly formed JSON string, null otherwise.
     */
    public function getErrorObject()
    {
        return json_decode($this->_httpResponse, true);
    }
}
