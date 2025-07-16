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
     * @param string $email the user email
     * @param string $password the user password
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
     * @param Payplug $payplug the client configuration
     *
     * @return  null|array the account settings
     *
     * @throws  Exception\ConfigurationNotSetException
     * @throws ConfigurationException
     */
    public static function getAccount($payplug = null)
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
     * @param Payplug $payplug the client configuration
     *
     * @return  null|array the account permissions
     *
     * @throws  Exception\ConfigurationNotSetException
     * @throws ConfigurationException
     */
    public static function getPermissions($payplug = null)
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
     * @param Payplug $payplug the client configuration
     *
     * @return array|false
     *createClientIdAndSecret
     * @throws  Exception
     */
    public static function getPublishableKeys($payplug = null)
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
     * Generate a token JWT from a given client id and secret
     *
     * @param string $client_id
     * @param string $client_secret
     *
     * @return array
     */
    public static function generateJWT($client_id = '', $client_secret = '')
    {
        if ($client_id == '') {
            return array();
        }
        if ($client_secret == '') {
            return array();
        }

        $httpClient = new Core\HttpClient(null);
        try {
            $route = Core\APIRoutes::getRoute(Core\APIRoutes::OAUTH2_TOKEN_RESOURCE, null, array(), array(), false);
            $response = $httpClient->post(
                $route,
                array(
                    'grant_type' => 'client_credentials',
                    'audience' => 'https://www.payplug.com',
                ), false, null, array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret)
            ),
                'x-www-form-urlencoded');

            if (!isset($response['httpResponse']) || empty($response['httpResponse'])) {
                return array();
            }

            $response['httpResponse']['expires_date'] = time() + $response['httpResponse']['expires_in'];

            return $response;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Generate a token JWT OneShot.
     *
     * @param string $authorization_code
     * @param string $callback_uri
     * @param string $client_id
     * @param string $code_verifier
     *
     * @return  array the token JWT OneShot
     *
     * @throws  Exception
     */
    public static function generateJWTOneShot($authorization_code='', $callback_uri='', $client_id = '', $code_verifier = '')
    {
        if ($authorization_code == '') {
            return array();
        }

        if ($callback_uri == '') {
            return array();
        }

        if ($client_id == '') {
            return array();
        }

        if ($code_verifier == '') {
            return array();
        }

        $httpClient = new Core\HttpClient(null);
        try {
            $route = Core\APIRoutes::getRoute(Core\APIRoutes::OAUTH2_TOKEN_RESOURCE, null, array(), array(), false);
            $response = $httpClient->post(
                $route,
                array(
                    'grant_type' => 'authorization_code',
                    'code' => $authorization_code,
                    'redirect_uri' => $callback_uri,
                    'client_id' => $client_id,
                    'code_verifier' => $code_verifier
                ),
                false,
                null,
                array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded'
                ),
                'application/x-www-form-urlencoded'
            );
        } catch (Exception $e) {
            $response = array();
        }

        return $response;
    }

    /**
     * Validates the Payplug token
     *
     * @param Payplug $payplug
     * @return void
     * @throws ConfigurationException
     */
    private static function validateToken($payplug)
    {
        $token = $payplug->getToken();
        if (empty($token)) {
            throw new ConfigurationException('The Payplug configuration requires a valid token.');
        }
    }

    /**
     * Create a client ID and secret for a given mode
     *
     * @param $company_id
     * @param $client_name
     * @param $mode
     * @param $session
     * @param Payplug|null $payplug
     *
     * @return array
     * @throws ConfigurationException
     * @throws Exception\ConfigurationNotSetException
     * @throws Exception\ConnectionException
     * @throws Exception\HttpException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public static function createClientIdAndSecret($company_id = '', $client_name = '', $mode = '', $session = null, $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug::getDefaultConfiguration();
        }

        $httpClient = new Core\HttpClient($payplug);
        $response = array();
        $route = Core\APIRoutes::getServiceRoute(Core\APIRoutes::CLIENT_RESOURCE);
        try {
            $response = $httpClient->post(
                $route,
                array(
                    'company_id' => $company_id,
                    'client_name' => $client_name,
                    'client_type' => 'client_credentials_flow',
                    'mode' => $mode,
                ));
        } catch (Exception $e) {
            return $response;
        }

        return $response;
    }

    /**
     * Get the return url to register user through the portal
     *
     * @param string $setup_redirection_uri
     * @param string $oauth_callback_uri
     *
     * @return array
     * @throws Exception\ConnectionException
     * @throws Exception\HttpException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public static function getRegisterUrl($setup_redirection_uri = '', $oauth_callback_uri = '')
    {
        if (empty($setup_redirection_uri)) {
            throw new Exception\ConfigurationException('Expected string values for setup redirection uri.');
        }
        if (empty($oauth_callback_uri)) {
            throw new Exception\ConfigurationException('Expected string values for oauth callback uri.');
        }

        $url_datas = array(
            'setup_redirection_uri' => $setup_redirection_uri,
            'oauth_callback_uri' => $oauth_callback_uri,
        );

        $route = Core\APIRoutes::getServiceRoute(Core\APIRoutes::PLUGIN_SETUP_SERVICE, $url_datas);

        return $route;
    }

    /**
     * Redirect to callback page and provide an authorization_code
     *
     * @param $client_id
     * @param $redirect_uri
     * @param $code_verifier
     * @param Payplug|null $payplug
     * @throws ConfigurationException
     * @throws Exception\ConfigurationNotSetException
     * @throws Exception\ConnectionException
     * @throws Exception\HttpException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public static function initiateOAuth($client_id='', $redirect_uri='', $code_verifier='')
    {
        $hash = hash("sha256", $code_verifier);
        $code_challenge = base64_encode(pack("H*", $hash));
        $code_challenge = strtr($code_challenge, "+/", "-_");
        $code_challenge = rtrim($code_challenge, "=");

        $portal_url_datas = array(
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'state' => bin2hex(openssl_random_pseudo_bytes(10)),
            'scope' => 'openid offline profile email',
            'audience' => 'https://www.payplug.com',
            'code_challenge' => $code_challenge,
            'code_challenge_method' => 'S256',
        );

        $portal_url = Core\APIRoutes::getRoute(Core\APIRoutes::OAUTH2_AUTH_RESOURCE, null, array(), $portal_url_datas, false);

        header("Location: $portal_url");
    }

    /**
     * Check if given token is expired and if so, regenerate a new one
     *
     * @param array $client_data
     * @param array $token
     *
     * @return array
     */
    public static function validateJWT($client_data = array(), $token = array())
    {
        if (!is_array($client_data) || empty($client_data)) {
            return array(
                'result' => false,
                'token' => null,
                'need_update' => false,
            );
        }
        if (!is_array($token) || empty($token)) {
            return array(
                'result' => false,
                'token' => null,
                'need_update' => false,
            );
        }

        $current_date = time();
        if ($token['expires_date'] > $current_date) {
            return array(
                'result' => true,
                'token' => $token,
                'need_update' => false,
            );
        }

        $token = self::generateJWT($client_data['client_id'], $client_data['client_secret']);
        if (empty($token) || !isset($token['httpResponse'])) {
            return array(
                'result' => false,
                'token' => null,
                'need_update' => false,
            );
        }

        return array(
            'result' => true,
            'token' => $token['httpResponse'],
            'need_update' => true,
        );
    }
}
