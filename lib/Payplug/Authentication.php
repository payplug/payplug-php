<?php
namespace Payplug;

use Exception;
use Payplug\Exception\ConfigurationException;

/**
 * The Authentication DAO simplifies the access to most useful customer methods
 **/
class Authentication
{
    /**
     * Retrieve existing API keys for an user, using his email and password.
     * This function is for user-friendly interface purpose only.
     * You should probably not use this more than once, login/password MUST NOT be stored and API Keys are enough to interact with API.
     *
     * @param   string $email the user email
     * @param   string $password the user password
     *
     * @return  null|array the API keys
     *
     * @throws  Exception\BadRequestException
     */
    public static function getKeysByLogin($email, $password)
    {
        $httpClient = new Core\HttpClient(null);
        $response = $httpClient->post(
            Core\APIRoutes::getRoute(Core\APIRoutes::KEY_RESOURCE),
            array('email' => $email, 'password' => $password),
            false
        );
        return $response;
    }

    /**
     * Retrieve account info.
     *
     * @param   Payplug $payplug the client configuration
     *
     * @return  null|array the account settings
     *
     * @throws  Exception\ConfigurationNotSetException
     * @throws ConfigurationException
     */
    public static function getAccount(Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug::getDefaultConfiguration();
        }
        self::validateToken($payplug);

        $httpClient = new Core\HttpClient($payplug);
        $response = $httpClient->get(Core\APIRoutes::getRoute(Core\APIRoutes::ACCOUNT_RESOURCE));

        return $response;
    }

    /**
     * Retrieve the account permissions
     *
     * @param  Payplug $payplug the client configuration
     *
     * @return  null|array the account permissions
     *
     * @throws  Exception\ConfigurationNotSetException
     * @throws ConfigurationException
     */
    public static function getPermissions(Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug::getDefaultConfiguration();
        }

        self::validateToken($payplug);

        $httpClient = new Core\HttpClient($payplug);
        $response = $httpClient->get(Core\APIRoutes::getRoute(Core\APIRoutes::ACCOUNT_RESOURCE));

        return $response['httpResponse']['permissions'];
    }

    /**
     * Retrieve the account permissions, using email and password.
     * This function is for user-friendly interface purpose only.
     * You should probably not use this more than once, login/password MUST NOT be stored and API Keys are enough to interact with API.
     *
     * @param string $email the user email
     * @param string $password the user password
     *
     * @return  null|array the account permissions
     *
     * @throws  Exception\ConfigurationNotSetException
     * @throws ConfigurationException
     */
    public static function getPermissionsByLogin($email, $password)
    {
        $keys = self::getKeysByLogin($email, $password);
        $payplug = Payplug::init(array(
            'secretKey' => $keys['httpResponse']['secret_keys']['live'],
            'apiVersion' => null,
        ));
        self::validateToken($payplug);

        $httpClient = new Core\HttpClient($payplug);
        $response = $httpClient->get(Core\APIRoutes::getRoute(Core\APIRoutes::ACCOUNT_RESOURCE));

        return $response['httpResponse']['permissions'];
    }

    /**
     * Retrieve publisable keys
     *
     * @param   Payplug $payplug the client configuration
     *
     * @return array|false
     *
     * @throws  Exception
     */
    public static function getPublishableKeys(Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug::getDefaultConfiguration();
        }
        $httpClient = new Core\HttpClient($payplug);
        try {
            $response = $httpClient->post(Core\APIRoutes::getRoute(Core\APIRoutes::PUBLISHABLE_KEYS));
            return $response;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validates the Payplug token
     *
     * @param Payplug $payplug
     * @return void
     * @throws ConfigurationException
     */
    private static function validateToken(Payplug $payplug)
    {
        $token = $payplug->getToken();
        if (empty($token)) {
            throw new ConfigurationException('The Payplug configuration requires a valid token.');
        }
    }
}
