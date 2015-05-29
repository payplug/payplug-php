<?php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__)));

require_once 'includes/exceptions/PayPlugException.php';
require_once 'includes/exceptions/ConfigurationNotSetException.php';
require_once 'includes/exceptions/ConnectionException.php';
require_once 'includes/exceptions/DependencyException.php';
require_once 'includes/exceptions/HttpException.php';
require_once 'includes/exceptions/InvalidPaymentException.php';
require_once 'includes/exceptions/UndefinedAttributeException.php';
require_once 'includes/exceptions/UnknownAPIResourceException.php';

require_once 'includes/APIRoutes.php';
require_once 'includes/ClientConfiguration.php';
require_once 'includes/HttpClient.php';
require_once 'includes/Notification.php';

require_once 'includes/api_resources/APIResource.php';
require_once 'includes/api_resources/IVerifiableAPIResource.php';

require_once 'includes/api_resources/resources/Card.php';
require_once 'includes/api_resources/resources/Customer.php';
require_once 'includes/api_resources/resources/HostedPayment.php';
require_once 'includes/api_resources/resources/Payment.php';
require_once 'includes/api_resources/resources/PaymentFailure.php';
require_once 'includes/api_resources/resources/Refund.php';

/**
 * Minimal configuration to launch the script.
 */
class PayPlug_Config
{
    /**
     * The library version
     */
    const LIBRARY_VERSION = '1.0.0';

    /**
     * PHP minimal version required by this library
     */
    const PHP_MIN_VERSION = '5.2.0';

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
if (version_compare(phpversion(), PayPlug_Config::PHP_MIN_VERSION, "<")) {
    throw new Exception('This library needs PHP ' . PayPlug_Config::PHP_MIN_VERSION . ' or newer.');
}

// Check PHP configuration
foreach(PayPlug_Config::$REQUIRED_FUNCTIONS as $key => $value) {
    if (!function_exists($key)) {
        throw new PayPlug_DependencyException('This library requires ' . $value . '.');
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
