<?php

/**
 * The core class of this library.
 */
class Payplug {

    const VERSION = "1.1.1";

    /**
     * The merchant's parameters which will be used to generate payment URLS.
     */
    private static $parameters;

    public static function getConfig() {
        return self::$parameters;
    }

    /**
     * Connects to Payplug, and retrieves the e-commerce parameters associated
     * to the account `email`.
     */
    public static function loadParameters($email, $password, $is_test=false) {
        $answer;
        $configUrl = 'https://www.payplug.fr/portal/ecommerce/autoconfig';
        if ($is_test === true) {
            $configUrl = 'https://www.payplug.fr/portal/test/ecommerce/autoconfig';
        }
        $curlErrNo;
        $curlErrMsg;
        $httpCode;
        $httpMsg;
        $parameters;
        $process = curl_init($configUrl);

        curl_setopt($process, CURLOPT_HEADER, true);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($process, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($process, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($process, CURLOPT_USERPWD, $email . ':' . $password);

        $answer = curl_exec($process);
        $headerSize = curl_getinfo($process, CURLINFO_HEADER_SIZE);
        // HTTP response code (200, 401, 404...)
        $httpCode = curl_getinfo($process, CURLINFO_HTTP_CODE);

        // Extracts JSON
        $body = substr($answer, $headerSize);
        // Extracts headers
        $headers = substr($answer, 0, $headerSize);
        // Splits the string containing the headers into lines in an array
        $headers = explode("\r\n", $headers);

        /*
         * Splits the first line (containing HTTP response details) by spaces
         * for the 2 first matches. The last one will contains the remaining
         * text which is equivalent to the readable HTTP response message.
         */
        $httpMsg = explode(" ", $headers[0], 3);
        // The well deserved message
        $httpMsg = $httpMsg[2];
        // Curl error code (different from the HTTP response code)
        $curlErrNo = curl_errno($process);
        // Human readable message
        $curlErrMsg = curl_error($process);
        curl_close($process);

        // If there was no error
        if ($curlErrNo == 0) {
            $body = json_decode($body);

            // Authentication OK
            if ($httpCode == 200) {
                $parameters = new Parameters(
                    $body->currencies,
                    $body->amount_max,
                    $body->amount_min,
                    $body->url,
                    $body->payplugPublicKey,
                    $body->yourPrivateKey
                );
            }
            // Wrong email and/or password
            elseif ($httpCode == 401) {
                throw new InvalidCredentialsException();
            }
            // Access Forbidden if account is not activate
            elseif ($httpCode == 403) {
                throw new ForbiddenCredentialsException($body->message);
            }
            // I wonder what this could be
            else {
                throw new NetworkException("HTTP error ($httpCode) : $httpMsg", $httpCode);
            }
        }
        else {
            throw new NetworkException("CURL error ($curlErrNo) : $curlErrMsg", $curlErrNo);
        }

        return $parameters;
    }

    public static function setConfig($parameters) {
        self::$parameters = $parameters;
    }

    public static function setConfigFromFile($path) {
        self::$parameters = Parameters::loadFromFile($path);
    }
}

