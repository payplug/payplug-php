<?php
namespace Payplug;

/**
* @group unit
* @group ci
* @group recommended
*/
class NotificationTest extends \PHPUnit_Framework_TestCase
{

    private $_requestMock;
    private $_configuration;

    protected function setUp()
    {
        $this->_configuration = new \Payplug\Payplug('abc', 'cba', true);
        \Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        \Payplug\Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    public function testTreatPayment()
    {

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{ "id": "real_payment", "object": "payment" }'));
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

        $body = '{ "id": "pay_123", "object": "payment" }';
        $payment = \Payplug\Notification::treat($body, $this->_configuration);
        $this->assertTrue($payment instanceof $payment);
        $this->assertEquals('real_payment', $payment->id);
    }

    public function testTreatWhenBodyIsNotValidJSON()
    {
        $this->setExpectedException('\PayPlug\Exception\UnknownAPIResourceException');

        $this->_requestMock
            ->expects($this->never())
            ->method('exec');

        $body = 'invalidJSON';
        \Payplug\Notification::treat($body, $this->_configuration);
    }
}