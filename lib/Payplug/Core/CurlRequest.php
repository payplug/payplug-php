<?php
namespace Payplug\Core;

/**
 * A Curl implementation of a IHttpRequest.
 */
class CurlRequest implements IHttpRequest
{
    /**
     * @var resource the curl object
     */
    private $_curl;

    /**
     * Payplug\CurlRequest constructor
     * Initializes a curl request
     */
    public function __construct()
    {
        $this->_curl = curl_init();
    }

    /**
     * @inheritDoc
     */
    public function setopt($option, $value)
    {
        return curl_setopt($this->_curl, $option, $value);
    }

    /**
     * @inheritDoc
     */
    public function getinfo($option)
    {
        return curl_getinfo($this->_curl, $option);
    }

    /**
     * @inheritDoc
     */
    public function exec()
    {
        return curl_exec($this->_curl);
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        // curl_close() is a no-op since PHP 8.0 and deprecated in PHP 8.5.
        // Calling it under PHP 8.5 with developer mode throws (deprecations -> exceptions).
        if (PHP_VERSION_ID < 80000) {
            curl_close($this->_curl);
        }
    }

    /**
     * @inheritDoc
     */
    public function error()
    {
        return curl_error($this->_curl);
    }

    /**
     * @inheritDoc
     */
    public function errno()
    {
        return curl_errno($this->_curl);
    }
}
