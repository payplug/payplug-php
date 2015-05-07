<?php

$extensions = array(
    "curl" => "cURL",
    "openssl" => "OpenSSL"
);
$functions = array(
    "base64_decode",
    "base64_encode",
    "json_decode",
    "json_encode",
    "urlencode"
);
$phpMin = "5.2.0";

// Checks that all required extensions have been loaded
foreach ($extensions as $name => $title) {
    if (!extension_loaded($name)) {
        throw new Exception("This library needs the $title extension.");
    }
}
// Checks that all required functions exist
foreach ($functions as $func) {
    if (!function_exists($func)) {
        throw new Exception("This library needs the '$func' function.");
    }
}

if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = array();

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
            } else if ($name == "CONTENT_TYPE") {
                $headers["Content-Type"] = $value;
            } else if ($name == "CONTENT_LENGTH") {
                $headers["Content-Length"] = $value;
            } else {
                $headers[$name] = $value;
            }
       }

       return $headers;
    }
}

// If the PHP version is strictly lower than 5.2.0
if (version_compare(phpversion(), $phpMin, "<")) {
    throw new Exception("This library needs PHP $phpMin or newer.");
}

require_once(__DIR__ . '/payplug/IPN.php');
require_once(__DIR__ . '/payplug/Parameters.php');
require_once(__DIR__ . '/payplug/PaymentUrl.php');
require_once(__DIR__ . '/payplug/Payplug.php');
require_once(__DIR__ . '/payplug/PayplugExceptions.php');

