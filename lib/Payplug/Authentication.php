<?php
namespace Payplug;

use Exception;

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
     * @param  string $email the user email
     * @param  string $password the user password
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
     */
    public static function getAccount(Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug::getDefaultConfiguration();
        }

        $httpClient = new Core\HttpClient($payplug);
        $response = $httpClient->get(Core\APIRoutes::getRoute(Core\APIRoutes::ACCOUNT_RESOURCE));

        return $response;
    }

    /**
     * Retrieve the account permissions
     *
     * @param   Payplug $payplug the client configuration
     *
     * @return  null|array the account permissions
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function getPermissions(Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug::getDefaultConfiguration();
        }

        $httpClient = new Core\HttpClient($payplug);
        $response = $httpClient->get(Core\APIRoutes::getRoute(Core\APIRoutes::ACCOUNT_RESOURCE));

        return $response['httpResponse']['permissions'];
    }

    /**
     * Retrieve the account permissions, using email and password.
     * This function is for user-friendly interface purpose only.
     * You should probably not use this more than once, login/password MUST NOT be stored and API Keys are enough to interact with API.
     *
     * @param   string $email the user email
     * @param   string $password the user password
     *
     * @return  null|array the account permissions
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function getPermissionsByLogin($email, $password)
    {
        $keys = self::getKeysByLogin($email, $password);
        $payplug = Payplug::init(array(
             'secretKey' => $keys['httpResponse']['secret_keys']['live'],
             'apiVersion' => null,
         ));

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
     * Retrieve client id and client_secret_mask from the user manager resource.
     *
     * @param   Payplug $payplug the client configuration
     *
     * @return  array the client id and client_secret_mask
     *
     * @throws  Exception
     */
    public static function getClientData(Payplug $payplug = null, $session = null)
    {
        if ($payplug === null) {
            $payplug = Payplug::getDefaultConfiguration();
        }
        $kratosSession = self::setKratosSession($session);

        $httpClient = new Core\HttpClient($payplug);
        $response = $httpClient->get(Core\APIRoutes::$USER_MANAGER_RESOURCE, null, $kratosSession);
        $result = array();

        foreach ($response['httpResponse'] as $client) {
            $result[] = array(
                'client_id' => $client['client_id'],
                'client_secret_mask' => $client['client_secret_mask'],
                'client_name' => $client['client_name'],
                'client_type' => $client['client_type'],
                'mode' => $client['mode'],

            );
        }

        return $result;
    }

    /**
     * Create a client ID and secret.
     * @param string $clientName The name of the client.
     * @param string $clientType The type of the client.
     * @param string $companyId The ID of the company.
     * @param string $mode The mode (e.g., test or live).
     * @param Payplug|null $payplug The Payplug configuration. If null, the default configuration is used.
     * @param string|null $session The session value to be set in the cookie.
     *
     * @return array The client ID and client secret.
     *
     * @throws Exception\ConfigurationNotSetException
     * @throws Exception\ConnectionException
     * @throws Exception\HttpException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public static function createClientIdAndSecret($clientName, $clientType, $companyId, $mode, Payplug $payplug = null, $session = null)
    {
        if ($payplug === null) {
            $payplug = Payplug::getDefaultConfiguration();
        }
        $kratosSession = self::setKratosSession($session);

        $httpClient = new Core\HttpClient($payplug);
        $response = $httpClient->post(Core\APIRoutes::$USER_MANAGER_RESOURCE, array(
            'company_id' => $companyId,
            'client_name' => $clientName,
            'client_type' => $clientType,
            'mode' => $mode,
        ), $kratosSession);

        $result = array();

        foreach ($response['httpResponse'] as $client) {
            $result[] = array(
                'client_id' => $client['client_id'],
                'client_secret' => $client['client_secret'],
            );
        }

        return $result;
    }


    /**
     * Set the Kratos session cookie.
     *
     * @param string $session The session value to be set in the cookie.
     *
     * @return string The formatted Kratos session cookie string.
     */
    public static function setKratosSession($session)
    {
        return 'ory_kratos_session=' . $session;
    }

}
