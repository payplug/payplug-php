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
     * @return  null|Payplug\Resource\APIResource|Card the card object
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
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
     * @param   string $payplug the user email
     *
     * @return  null|Payplug\Resource\APIResource|Card the card object
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
     * Retrieve existing API keys for an user, using his email and password.
     *
     * @param   string $payplug the user email
     *
     * @return  null|Payplug\Resource\APIResource|Card the card object
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
     * Retrieve existing API keys for an user, using his email and password.
     *
     * @param   string $payplug the user email
     *
     * @return  null|Payplug\Resource\APIResource|Card the card object
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
