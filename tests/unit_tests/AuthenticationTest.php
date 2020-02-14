<?php
namespace Payplug;
use Payplug;
use Payplug\Core\HttpClient;

/**
* @group unit
* @group ci
* @group recommended
*/
class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    private $_requestMock;

    protected function setUp()
    {
        $this->_configuration = new \Payplug\Payplug('abc');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    protected function setUpTwice()
    {
        $this->_configuration = new \Payplug\Payplug('abc','1970-01-01');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
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
}
