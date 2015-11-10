<?php
namespace Payplug\Core;

/**
 * Generified HttpRequest so that it can easily be mocked
 */
interface IHttpRequest
{
    /**
     * Simple wrapper for curl_setopt
     *
     * @link http://php.net/manual/en/function.curl-setopt.php
     */
    function setopt($option, $value);

    /**
     * Simple wrapper for curl_exec
     *
     * @link http://php.net/manual/en/function.curl-exec.php
     */
    function exec();

    /**
     * Simple wrapper for curl_getinfo
     *
     * @link http://php.net/manual/en/function.curl-getinfo.php
     */
    function getinfo($option);

    /**
     * Simple wrapper for curl_close
     *
     * @link http://php.net/manual/en/function.curl-close.php
     */
    function close();

    /**
     * Simple wrapper for curl_error
     *
     * @link http://php.net/manual/en/function.curl-close.php
     */
    function error();

    /**
     * Simple wrapper for curl_errno
     *
     * @link http://php.net/manual/en/function.curl-close.php
     */
    function errno();
}
