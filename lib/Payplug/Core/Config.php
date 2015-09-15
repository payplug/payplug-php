<?php
namespace Payplug\Core;

/**
 * Minimal configuration to launch the script.
 */
class Config
{
    /**
     * The library version
     */
    const LIBRARY_VERSION = '2.0.0';

    /**
     * PHP minimal version required by this library
     */
    const PHP_MIN_VERSION = '5.3.0';

    /**
     * array   A dictionary whose keys are the name of the function required by this library and whose values are the
     * corresponding dependencies.
     * For instance:
     * <pre>
     *  'curl_version' => 'php5-curl'
     * </pre>
     * means that this program requires curl_version() function to work properly and that it corresponds to php5-curl
     * package.
     */
    public static $REQUIRED_FUNCTIONS = array(
        'json_decode'   => 'php5-json',
        'json_encode'   => 'php5-json',
        'curl_version'  => 'php5-curl'
    );
}

// Check PHP version
if (version_compare(phpversion(), Config::PHP_MIN_VERSION, "<")) {
    throw new Exception('This library needs PHP ' . \Payplug\Core\Config::PHP_MIN_VERSION . ' or newer.');
}

// Check PHP configuration
foreach(Config::$REQUIRED_FUNCTIONS as $key => $value) {
    if (!function_exists($key)) {
        throw new \Payplug\Exception\DependencyException('This library requires ' . $value . '.');
    }
}

// Prior to PHP 5.5, CURL_SSLVERSION_TLSv1 and CURL_SSLVERSION_DEFAULT didn't exist. Hence, we have to use a numeric value.
if (!defined('CURL_SSLVERSION_DEFAULT')) {
    /** @ignore */
    define('CURL_SSLVERSION_DEFAULT', 0);
}
if (!defined('CURL_SSLVERSION_TLSv1')) {
    /** @ignore */
    define('CURL_SSLVERSION_TLSv1', 1);
}
