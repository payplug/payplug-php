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
        $this->_configuration = new \Payplug\Payplug('abc','1970-01-01');
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
            ->will($this->returnCallback(function($option) {
                switch($option) {
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
            ->will($this->returnCallback(function($option) {
                switch($option) {
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
            ->will($this->returnCallback(function($option) {
                switch($option) {
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
            ->will($this->returnCallback(function($option) {
                switch($option) {
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
     * Test the getClientIdAndSecretMask method.
     *
     * This test verifies that the getClientData method correctly retrieves
     * the client_id, client_secret_mask , client_name client_type and mode from the user manager resource.
     *
     * @throws \Exception
     */
    public function testGetClientData()
    {
        $response = array(
            array(
                'client_id' => 'test_client_id',
                'client_secret_mask' => 'test_secret_mask',
                'client_name' => 'test_client_name',
                'client_type' => 'test_client_type',
                'mode' => 'test_mode',
            ),
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

        $session = 'test_session_value';
        $result = Authentication::getClientData($session, $this->_configuration);
        $this->assertCount(1, $result);
        $this->assertEquals('test_client_id', $result[0]['client_id']);
        $this->assertEquals('test_secret_mask', $result[0]['client_secret_mask']);
        $this->assertEquals('test_client_name', $result[0]['client_name']);
        $this->assertEquals('test_client_type', $result[0]['client_type']);
        $this->assertEquals('test_mode', $result[0]['mode']);

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
            ->will($this->returnCallback(function($option) {
                switch($option) {
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
        $this->assertCount(1, $result);
        $this->assertEquals('test_client_id', $result[0]['client_id']);
        $this->assertEquals('test_client_secret', $result[0]['client_secret']);
    }

    /**
     * Test the setKratosSession method with a null session.
     */
    public function testSetKratosSessionNull()
    {
        $this->expectException('\PayPlug\Exception\ConfigurationException');
        $this->expectExceptionMessage('The session value must be set.');
        Authentication::setKratosSession(null);
    }
}
