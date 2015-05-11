<?php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__)));

require_once 'includes/exceptions/PayPlugException.php';
require_once 'includes/exceptions/ConfigurationNotSetException.php';
require_once 'includes/exceptions/DependencyException.php';
require_once 'includes/exceptions/PaymentException.php';
require_once 'includes/exceptions/PayPlugHttpException.php';

require_once 'includes/APIRoutes.php';
require_once 'includes/ClientConfiguration.php';
require_once 'includes/HttpClient.php';

require_once 'includes/api_resources/APIResource.php';
require_once 'includes/api_resources/Card.php';
require_once 'includes/api_resources/Customer.php';
require_once 'includes/api_resources/HostedPayment.php';
require_once 'includes/api_resources/Payment.php';
require_once 'includes/api_resources/PaymentFailure.php';
require_once 'includes/api_resources/Refund.php';

/**
 * Class PayPlug_CONFIG
 */
class PayPlug_CONFIG
{
    const PHP_MIN = '5.2.0';
    public static $REQUIRED_FUNCTIONS = array(
        'json_decode'   => 'php5-json',
        'json_encode'   => 'php5-json',
        'curl_version'  => 'php5-curl'
    );
}

if (version_compare(phpversion(), PayPlug_CONFIG::PHP_MIN, "<")) {
    throw new Exception('This library needs PHP ' . PayPlug_CONFIG::PHP_MIN . ' or newer.');
}

foreach(PayPlug_CONFIG::$REQUIRED_FUNCTIONS as $key => $value) {
    if (!function_exists($key)) {
        throw new PayPlug_DependencyException('This library requires ' . $value . '.');
    }
}