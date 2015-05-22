<?php

/**
 * HTTP errors
 */
class PayPlug_HttpException extends PayPlug_PayPlugException
{
    private $_httpResponse;

    /**
     * @param string $message the exception message
     * @param string $httpResponse the http response content
     * @param int $code the exception code
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
     * @return string the HTTP response
     */
    public function getHttpResponse()
    {
        return $this->_httpResponse;
    }

    /**
     * @return array|null the error array if it was a valid JSON, null otherwise.
     */
    public function getErrorObject()
    {
        return json_decode($this->_httpResponse, true);
    }
}

/**
 * 400 Bad Request
 */
class PayPlug_BadRequestException extends PayPlug_HttpException
{
}

/**
 * 401 Unauthorized
 */
class PayPlug_UnauthorizedException extends PayPlug_HttpException
{
}

/**
 * 403 Forbidden
 */
class PayPlug_ForbiddenException extends PayPlug_HttpException
{
}

/**
 * 404 Not Found
 */
class PayPlug_NotFoundException extends PayPlug_HttpException
{
}

/**
 * 405 Not Allowed
 */
class PayPlug_NotAllowedException extends PayPlug_HttpException
{
}

/**
 * 5XX server errors
 */
class PayPlug_PayPlugServerException extends PayPlug_HttpException
{
}

/**
 * Thrown when we expected the API to have a specific format, and we got something else.
 */
class PayPlug_UnexpectedAPIResponseException extends PayPlug_HttpException
{
}