<?php
namespace Payplug;
use Payplug;

/**
 * 
 */
class Authentication
{
    /**
     * Retrieve existing API keys for an user, using his email and password.
     *
     * @param   string $email the user email
     * @param   string $password the user password
     *
     * @return  null|array the API keys
     *
     * @throws  Payplug\Exception\BadRequestException
     */
    public static function getKeysByLogin($email, $password)
    {
        $payplug = new Payplug\Payplug('');
        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->post(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::KEY_RESOURCE),
            array('email' => $email, 'password' => $password),
            false
        );
        return $response;
    }

    /**
     * Retrieve account info.
     *
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @return  null|array the account settings
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function getAccount(Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->get(Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::ACCOUNT_RESOURCE));

        return $response;
    }

    /**
     * Retrieve the account permissions
     *
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @return  null|array the account permissions
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function getPermissions(Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->get(Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::ACCOUNT_RESOURCE));

        return $response['httpResponse']['permissions'];
    }

    /**
     * Retrieve the account permissions, using email and password.
     *
     * @param   string $email the user email
     * @param   string $password the user password
     *
     * @return  null|array the account permissions
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function getPermissionsByLogin($email, $password)
    {
        $keys = self::getKeysByLogin($email, $password);
        $payplug = Payplug\Payplug::setSecretKey($keys['httpResponse']['secret_keys']['live']);
        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->get(Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::ACCOUNT_RESOURCE));

        return $response['httpResponse']['permissions'];
    }
}
