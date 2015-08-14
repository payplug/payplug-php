<?php
namespace Payplug\Exception;

/**
 * HTTP errors
 */
class HttpException extends \Payplug\Exception\PayPlugException
{
    /**
     * @var null|string The plain HTTP response.
     */
    private $_httpResponse;

    /**
     * \Payplug\Exception\HttpException constructor.
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

/**
 * 400 Bad Request
 */
class BadRequestException extends HttpException
{
}

/**
 * 401 Unauthorized
 */
class UnauthorizedException extends HttpException
{
}

/**
 * 403 Forbidden
 */
class ForbiddenException extends HttpException
{
}

/**
 * 404 Not Found
 */
class NotFoundException extends HttpException
{
}

/**
 * 405 Not Allowed
 */
class NotAllowedException extends HttpException
{
}

/**
 * 5XX server errors
 */
class PayPlugServerException extends HttpException
{
}

/**
 * Thrown when we expected the API to have a specific format, and we got something else.
 */
class UnexpectedAPIResponseException extends HttpException
{
}
