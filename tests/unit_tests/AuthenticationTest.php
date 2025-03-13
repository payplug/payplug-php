<?php

namespace Payplug;

use Payplug;
use Payplug\Core\HttpClient;
use Payplug\Exception\ConfigurationException;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class AuthenticationTest extends \PHPUnit\Framework\TestCase
{
    private $_configuration;
    private $_requestMock;

    /**
     * @before
     */
    protected function setUpTest()
    {
        $this->_configuration = new \Payplug\Payplug('abc');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->createMock('\Payplug\Core\IHttpRequest');
        Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    protected function setUpTwice()
    {
        $this->_configuration = new \Payplug\Payplug('abc', '1970-01-01');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->createMock('\Payplug\Core\IHttpRequest');
        Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    public function testGetKeysByLogin()
    {
        $email = 'test@fakemail.com';
        $password = 'passwordIsOverrated';
        $response = array(
            'secret_keys' => array(
                'test' => 'sk_test_everythingIsUnderControl',
                'live' => 'sk_live_allYourBasesAreBelongToUs',
            ),
        );
        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode($response)));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function ($option) {
                switch ($option) {
                    case CURLINFO_HTTP_CODE:
                        return 201;
                }
                return null;
            }));

        $authentication = Authentication::getKeysByLogin($email, $password);

        $this->assertEquals(201, $authentication['httpStatus']);
        $this->assertEquals('sk_test_everythingIsUnderControl', $authentication['httpResponse']['secret_keys']['test']);
        $this->assertEquals('sk_live_allYourBasesAreBelongToUs', $authentication['httpResponse']['secret_keys']['live']);
    }

    public function testGetAccount()
    {
        $response = array(
            'is_live' => true,
            'object' => 'account',
            'id' => '12345',
            'configuration' => array(
                'currencies' => array(),
                'min_amounts' => array(),
                'max_amounts' => array(),
            ),
            'permissions' => array(
                'use_live_mode' => true,
                'can_save_cards' => false,
            ),
        );
        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode($response)));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function ($option) {
                switch ($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $account = Authentication::getAccount($this->_configuration);

        $this->assertEquals(200, $account['httpStatus']);
        $this->assertEquals(true, $account['httpResponse']['is_live']);
        $this->assertEquals('account', $account['httpResponse']['object']);
        $this->assertEquals('12345', $account['httpResponse']['id']);
    }

    /**
     *  Tests the getAccount method when no secret key is provided.
     *
     * @return void
     * @throws ConfigurationException
     */
    public function testGetAccountWithoutSecretKey()
    {
        $payplug = new \Payplug\Payplug('');
        Payplug\Payplug::setDefaultConfiguration($payplug);

        $this->expectException('\Payplug\Exception\ConfigurationException');
        $this->expectExceptionMessage('The Payplug configuration requires a valid token.');

        Authentication::getAccount();
    }

    public function testGetPermissions()
    {
        $response = array(
            'is_live' => true,
            'object' => 'account',
            'id' => '12345',
            'configuration' => array(
                'currencies' => array(),
                'min_amounts' => array(),
                'max_amounts' => array(),
            ),
            'permissions' => array(
                'can_use_oney' => true,
                'use_live_mode' => false,
                'can_create_deferred_payment' => true,
                'can_create_installment_plan' => false,
                'can_save_cards' => false,
            ),
        );
        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode($response)));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function ($option) {
                switch ($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $permissions = Authentication::getPermissions($this->_configuration);

        $this->assertEquals(true, $permissions['can_use_oney']);
        $this->assertEquals(false, $permissions['use_live_mode']);
        $this->assertEquals(true, $permissions['can_create_deferred_payment']);
        $this->assertEquals(false, $permissions['can_create_installment_plan']);
        $this->assertEquals(false, $permissions['can_save_cards']);
    }

    /**
     * Tests the getPermissions method when no secret key is provided.
     * @return void
     * @throws ConfigurationException
     */
    public function testGetPermissionsWithoutSecretKey()
    {
        $payplug = new \Payplug\Payplug('');
        Payplug\Payplug::setDefaultConfiguration($payplug);

        $this->expectException('\PayPlug\Exception\ConfigurationException');
        $this->expectExceptionMessage('The Payplug configuration requires a valid token.');
        Authentication::getPermissions();
    }

    public function testPublishableKeys()
    {
        $response = array(
            'publishable_key' => 'pk_test_everythingIsUnderControl'
        );

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode($response)));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function ($option) {
                switch ($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $publishable_keys = Authentication::getPublishableKeys($this->_configuration);

        $this->assertEquals(200, $publishable_keys['httpStatus']);
        $this->assertEquals('pk_test_everythingIsUnderControl', $publishable_keys['httpResponse']['publishable_key']);
    }

    /**
     * Test the createClientIdAndSecret correctly creates
     *  a client ID and client secret.
     *
     * @throws \Exception
     */
    public function testCreateClientIdAndSecret()
    {
        $response = array(
            array(
                'client_id' => 'test_client_id',
                'client_secret' => 'test_client_secret',
            ),
        );

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode($response)));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function ($option) {
                switch ($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));
        $session = 'test_session_value';
        $company_id = 'test_company_id';
        $client_name = 'test_client_name';
        $mode = 'test';
        $result = Authentication::createClientIdAndSecret($company_id, $client_name, $mode, $session, $this->_configuration);
        $client_data = $result['httpResponse'];
        $this->assertCount(1, $client_data);
        $this->assertEquals('test_client_id', $client_data[0]['client_id']);
        $this->assertEquals('test_client_secret', $client_data[0]['client_secret']);
    }

    public function testGenerateJWTOSWithEmptyClientId()
    {
        $jwt = Authentication::generateJWTOneShot($this->_configuration);
        $this->assertEquals(array(), $jwt);
    }

    public function testGenerateJWTWithEmptyClientId()
    {
        $jwt = Authentication::generateJWT('', 'client_secret');
        $this->assertEquals(array(), $jwt);
    }

    public function testGenerateJWTWhenErrorResponse()
    {
        $response = array(
            'access_token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImE2NmQxZWU3LTQzMWMtNDNiYS04NzA4LWQ1MzNkNTVmZjhlZCIsInR5cCI6IkpXVCJ9.eyJhdWQiOltdLCJjbGllbnRfaWQiOiIyNzdlNDk4MS0yYTBjLTQ4NGEtYTE3Ni1hZWNhOWRjNDhkNTQiLCJleHAiOjE3Mjc5NjU3OTQsImV4dCI6eyJjb21wYW55X2lkIjoiZDE0NzQ1ZmQtMTc5Yy00N2IxLTlkZDgtMTk3OTVmMzQ1MjJiIiwicmVhbG1faWQiOiIxNTM0N2NjNy0xZThmLTQzYzMtYjJjZi1iZDMxY2M5ZWU4YTEiLCJyZXNvdXJjZV9hY2Nlc3MiOnsiYXBpIjp7InJvbGVzIjpbIk1FUkNIQU5UX0FQUFMiXX19fSwiaWF0IjoxNzI3OTYyMTk0LCJpc3MiOiJodHRwczovLzEyNy4wLjAuMTo0NDQ0IiwianRpIjoiMGNhMzUyOWItOGQ5Zi00NDQxLWEyNDAtMGZhY2YyNDNmN2JiIiwibmJmIjoxNzI3OTYyMTk0LCJzY3AiOltdLCJzdWIiOiIyNzdlNDk4MS0yYTBjLTQ4NGEtYTE3Ni1hZWNhOWRjNDhkNTQifQ.K1pavlVMz4D4cAJtL8IklLXIos7ZjiC9YSofb343uLkjXdhbgel23k0GyEE_JkQ2xSSB46XLYQp0j-M1AaJIoNjCfVR-O1yWNpLYnLM07ECETO3kQc63vcvzOm5trn5oBq_T3FE78EmAIA5B3oaSu_m5_qUBci_C7oM0ItMMIpFnKYqk2ta8y2eUFFPu7detxJRLlBK4I7hW0xAt07GNhfyRl8eN7twC3aYFFkUejZmuEB_FmZkj7OqZDsNblDR21Ci_cahuZmt9WOmIqTW58l7wxXOB9vq5APtBi2LpQtE52ARofUNCWWOK1KHz_vSQlGiGDM6_56K85Whfkkj-LYvLRRZUuIXE2m528JTtnCarZr7Md5P9zHQOZyIbcWrjiUdM_daI1vELPT4UaCBIfpy-vY0wywiPtoosokNsFQNrwxo8f9affUkiuEwZedK9sreDfVL_tmrz5Bh6XzxMZB5ZiVQckUUPF7LKrBB8qDwotYvwdLIN-Wy3l2IeeTzF_NOFmO6mrNift2RhSQZP0s7Xfn2dVK1eiyO4gERNrsPvasb9nB15PIQ57wwWKzN8ue6z9utAX6YThTgc2fadrOWYMeo2W4c7KmuKr9hhLK2ThIWRM7H5rQq3H7Ke5AzlKyCQdgFlQzSl0O1gjzA0T4AnfuNW1zNedSfUsqMJCfM',
            'expires_in' => 300,
            'scope' => 'sandbox',
            'token_type' => 'bearer'
        );

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode($response)));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 401;
                }
                return null;
            }));

        $jwt = Authentication::generateJWT('client_id', 'client_secret');

        $this->assertEquals(array(), $jwt);
    }

    public function testGenerateJWTOSWhenErrorResponse()
    {
        $response = array(
            'access_token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImE2NmQxZWU3LTQzMWMtNDNiYS04NzA4LWQ1MzNkNTVmZjhlZCIsInR5cCI6IkpXVCJ9.eyJhdWQiOltdLCJjbGllbnRfaWQiOiIyNzdlNDk4MS0yYTBjLTQ4NGEtYTE3Ni1hZWNhOWRjNDhkNTQiLCJleHAiOjE3Mjc5NjU3OTQsImV4dCI6eyJjb21wYW55X2lkIjoiZDE0NzQ1ZmQtMTc5Yy00N2IxLTlkZDgtMTk3OTVmMzQ1MjJiIiwicmVhbG1faWQiOiIxNTM0N2NjNy0xZThmLTQzYzMtYjJjZi1iZDMxY2M5ZWU4YTEiLCJyZXNvdXJjZV9hY2Nlc3MiOnsiYXBpIjp7InJvbGVzIjpbIk1FUkNIQU5UX0FQUFMiXX19fSwiaWF0IjoxNzI3OTYyMTk0LCJpc3MiOiJodHRwczovLzEyNy4wLjAuMTo0NDQ0IiwianRpIjoiMGNhMzUyOWItOGQ5Zi00NDQxLWEyNDAtMGZhY2YyNDNmN2JiIiwibmJmIjoxNzI3OTYyMTk0LCJzY3AiOltdLCJzdWIiOiIyNzdlNDk4MS0yYTBjLTQ4NGEtYTE3Ni1hZWNhOWRjNDhkNTQifQ.K1pavlVMz4D4cAJtL8IklLXIos7ZjiC9YSofb343uLkjXdhbgel23k0GyEE_JkQ2xSSB46XLYQp0j-M1AaJIoNjCfVR-O1yWNpLYnLM07ECETO3kQc63vcvzOm5trn5oBq_T3FE78EmAIA5B3oaSu_m5_qUBci_C7oM0ItMMIpFnKYqk2ta8y2eUFFPu7detxJRLlBK4I7hW0xAt07GNhfyRl8eN7twC3aYFFkUejZmuEB_FmZkj7OqZDsNblDR21Ci_cahuZmt9WOmIqTW58l7wxXOB9vq5APtBi2LpQtE52ARofUNCWWOK1KHz_vSQlGiGDM6_56K85Whfkkj-LYvLRRZUuIXE2m528JTtnCarZr7Md5P9zHQOZyIbcWrjiUdM_daI1vELPT4UaCBIfpy-vY0wywiPtoosokNsFQNrwxo8f9affUkiuEwZedK9sreDfVL_tmrz5Bh6XzxMZB5ZiVQckUUPF7LKrBB8qDwotYvwdLIN-Wy3l2IeeTzF_NOFmO6mrNift2RhSQZP0s7Xfn2dVK1eiyO4gERNrsPvasb9nB15PIQ57wwWKzN8ue6z9utAX6YThTgc2fadrOWYMeo2W4c7KmuKr9hhLK2ThIWRM7H5rQq3H7Ke5AzlKyCQdgFlQzSl0O1gjzA0T4AnfuNW1zNedSfUsqMJCfM',
            'expires_in' => 300,
            'scope' => 'sandbox',
            'token_type' => 'bearer'
        );

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode($response)));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 401;
                }
                return null;
            }));

        $jwt = Authentication::generateJWTOneShot('some_authorization_code', 'some_callback_uri', 'some_client_id', $this->_configuration);

        $this->assertEquals(array(), $jwt);
    }

    public function testGenerateJWTWhenSuccessResponse()
    {
        $response = array(
            'access_token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImE2NmQxZWU3LTQzMWMtNDNiYS04NzA4LWQ1MzNkNTVmZjhlZCIsInR5cCI6IkpXVCJ9.eyJhdWQiOltdLCJjbGllbnRfaWQiOiIyNzdlNDk4MS0yYTBjLTQ4NGEtYTE3Ni1hZWNhOWRjNDhkNTQiLCJleHAiOjE3Mjc5NjU3OTQsImV4dCI6eyJjb21wYW55X2lkIjoiZDE0NzQ1ZmQtMTc5Yy00N2IxLTlkZDgtMTk3OTVmMzQ1MjJiIiwicmVhbG1faWQiOiIxNTM0N2NjNy0xZThmLTQzYzMtYjJjZi1iZDMxY2M5ZWU4YTEiLCJyZXNvdXJjZV9hY2Nlc3MiOnsiYXBpIjp7InJvbGVzIjpbIk1FUkNIQU5UX0FQUFMiXX19fSwiaWF0IjoxNzI3OTYyMTk0LCJpc3MiOiJodHRwczovLzEyNy4wLjAuMTo0NDQ0IiwianRpIjoiMGNhMzUyOWItOGQ5Zi00NDQxLWEyNDAtMGZhY2YyNDNmN2JiIiwibmJmIjoxNzI3OTYyMTk0LCJzY3AiOltdLCJzdWIiOiIyNzdlNDk4MS0yYTBjLTQ4NGEtYTE3Ni1hZWNhOWRjNDhkNTQifQ.K1pavlVMz4D4cAJtL8IklLXIos7ZjiC9YSofb343uLkjXdhbgel23k0GyEE_JkQ2xSSB46XLYQp0j-M1AaJIoNjCfVR-O1yWNpLYnLM07ECETO3kQc63vcvzOm5trn5oBq_T3FE78EmAIA5B3oaSu_m5_qUBci_C7oM0ItMMIpFnKYqk2ta8y2eUFFPu7detxJRLlBK4I7hW0xAt07GNhfyRl8eN7twC3aYFFkUejZmuEB_FmZkj7OqZDsNblDR21Ci_cahuZmt9WOmIqTW58l7wxXOB9vq5APtBi2LpQtE52ARofUNCWWOK1KHz_vSQlGiGDM6_56K85Whfkkj-LYvLRRZUuIXE2m528JTtnCarZr7Md5P9zHQOZyIbcWrjiUdM_daI1vELPT4UaCBIfpy-vY0wywiPtoosokNsFQNrwxo8f9affUkiuEwZedK9sreDfVL_tmrz5Bh6XzxMZB5ZiVQckUUPF7LKrBB8qDwotYvwdLIN-Wy3l2IeeTzF_NOFmO6mrNift2RhSQZP0s7Xfn2dVK1eiyO4gERNrsPvasb9nB15PIQ57wwWKzN8ue6z9utAX6YThTgc2fadrOWYMeo2W4c7KmuKr9hhLK2ThIWRM7H5rQq3H7Ke5AzlKyCQdgFlQzSl0O1gjzA0T4AnfuNW1zNedSfUsqMJCfM',
            'expires_in' => 300,
            'scope' => 'sandbox',
            'token_type' => 'bearer'
        );

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode($response)));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $jwt = Authentication::generateJWT('client_id', 'client_secret');

        $this->assertEquals(200, $jwt['httpStatus']);
        $this->assertEquals($response, $jwt['httpResponse']);
    }

    public function testGenerateJWTOSWhenSuccessResponse()
    {
        $response = array(
            'access_token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImE2NmQxZWU3LTQzMWMtNDNiYS04NzA4LWQ1MzNkNTVmZjhlZCIsInR5cCI6IkpXVCJ9.eyJhdWQiOltdLCJjbGllbnRfaWQiOiIyNzdlNDk4MS0yYTBjLTQ4NGEtYTE3Ni1hZWNhOWRjNDhkNTQiLCJleHAiOjE3Mjc5NjU3OTQsImV4dCI6eyJjb21wYW55X2lkIjoiZDE0NzQ1ZmQtMTc5Yy00N2IxLTlkZDgtMTk3OTVmMzQ1MjJiIiwicmVhbG1faWQiOiIxNTM0N2NjNy0xZThmLTQzYzMtYjJjZi1iZDMxY2M5ZWU4YTEiLCJyZXNvdXJjZV9hY2Nlc3MiOnsiYXBpIjp7InJvbGVzIjpbIk1FUkNIQU5UX0FQUFMiXX19fSwiaWF0IjoxNzI3OTYyMTk0LCJpc3MiOiJodHRwczovLzEyNy4wLjAuMTo0NDQ0IiwianRpIjoiMGNhMzUyOWItOGQ5Zi00NDQxLWEyNDAtMGZhY2YyNDNmN2JiIiwibmJmIjoxNzI3OTYyMTk0LCJzY3AiOltdLCJzdWIiOiIyNzdlNDk4MS0yYTBjLTQ4NGEtYTE3Ni1hZWNhOWRjNDhkNTQifQ.K1pavlVMz4D4cAJtL8IklLXIos7ZjiC9YSofb343uLkjXdhbgel23k0GyEE_JkQ2xSSB46XLYQp0j-M1AaJIoNjCfVR-O1yWNpLYnLM07ECETO3kQc63vcvzOm5trn5oBq_T3FE78EmAIA5B3oaSu_m5_qUBci_C7oM0ItMMIpFnKYqk2ta8y2eUFFPu7detxJRLlBK4I7hW0xAt07GNhfyRl8eN7twC3aYFFkUejZmuEB_FmZkj7OqZDsNblDR21Ci_cahuZmt9WOmIqTW58l7wxXOB9vq5APtBi2LpQtE52ARofUNCWWOK1KHz_vSQlGiGDM6_56K85Whfkkj-LYvLRRZUuIXE2m528JTtnCarZr7Md5P9zHQOZyIbcWrjiUdM_daI1vELPT4UaCBIfpy-vY0wywiPtoosokNsFQNrwxo8f9affUkiuEwZedK9sreDfVL_tmrz5Bh6XzxMZB5ZiVQckUUPF7LKrBB8qDwotYvwdLIN-Wy3l2IeeTzF_NOFmO6mrNift2RhSQZP0s7Xfn2dVK1eiyO4gERNrsPvasb9nB15PIQ57wwWKzN8ue6z9utAX6YThTgc2fadrOWYMeo2W4c7KmuKr9hhLK2ThIWRM7H5rQq3H7Ke5AzlKyCQdgFlQzSl0O1gjzA0T4AnfuNW1zNedSfUsqMJCfM',
            'expires_in' => 300,
            'scope' => 'sandbox',
            'token_type' => 'bearer'
        );

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode($response)));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $jwt = Authentication::generateJWTOneShot('some_authorization_code', 'some_callback_uri', 'some_client_id', $this->_configuration);

        $this->assertEquals(200, $jwt['httpStatus']);
        $this->assertEquals($response, $jwt['httpResponse']);
    }

    public function testGetRegisterUrl()
    {
        $setup_redirection_uri = 'setup.redirection.uri.com';
        $oauth_callback_uri = 'oauth.callback.uri.com';
        $register_url = Authentication::getRegisterUrl($setup_redirection_uri, $oauth_callback_uri);
        $parameters = array(
            'setup_redirection_uri' => $setup_redirection_uri,
            'oauth_callback_uri' => $oauth_callback_uri,
        );
        $expect = Core\APIRoutes::$SERVICE_BASE_URL . Core\APIRoutes::PLUGIN_SETUP_SERVICE . '?' . http_build_query($parameters);
        $this->assertEquals($expect, $register_url);
    }
}
