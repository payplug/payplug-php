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
     * Generate a token JWT.
     *
     * @param   Payplug $payplug the client configuration
     *
     * @return  array the token JWT
     *
     * @throws  Exception
     */
    public static function generateJWT($client_id = '', Payplug $payplug = null)
    {
        if ($client_id == '') {
            return array();
        }

        if ($payplug === null) {
            $payplug = Payplug::getDefaultConfiguration();
        }

        $httpClient = new Core\HttpClient($payplug);
        try {
            return $httpClient->post(
                Core\APIRoutes::getRoute(Core\APIRoutes::$HYDRA_RESOURCE),
                array('client_id' => $client_id, 'grant_type' => 'client_credentials')
            );
        } catch (Exception $e) {
            return array();
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

    /**
     * Retrieve client datas from the user manager resource.
     *
     * @param   Payplug $payplug the client configuration
     *
     * @return  array the client id and client_secret_mask
     *
     * @throws  Exception
     */
    public static function getClientData($session = null, Payplug $payplug = null)
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
     * Create a client ID and secret for a given mode
     *
     * @param $company_id
     * @param $client_name
     * @param $mode
     * @param $session
     * @param Payplug|null $payplug
     * @return array
     * @throws ConfigurationException
     * @throws Exception\ConfigurationNotSetException
     * @throws Exception\ConnectionException
     * @throws Exception\HttpException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public static function createClientIdAndSecret($company_id='', $client_name='', $mode='', $session = null, Payplug $payplug = null)
    {

        if ($payplug === null) {
            $payplug = Payplug::getDefaultConfiguration();
        }
        $kratosSession = self::setKratosSession($session);

        $httpClient = new Core\HttpClient($payplug);
        $result = array();

            $response = $httpClient->post(Core\APIRoutes::$USER_MANAGER_RESOURCE, array(
                'company_id' => $company_id,
                'client_name' => $client_name,
                'client_type' =>'oauth2',
                'mode' => $mode,
               ),  $kratosSession);
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
     * @throws ConfigurationException
     */
    public static function setKratosSession($session)
    {
        if (empty($session)) {
            throw new ConfigurationException('The session value must be set.');
        }
        return 'ory_kratos_session=' . $session;
    }

}
