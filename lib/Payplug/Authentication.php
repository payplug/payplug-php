<?php
namespace Payplug;

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
}
