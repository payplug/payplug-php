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
        $this->_configuration = new \Payplug\Payplug('abc');
        Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    protected function setUpTwice()
    {
        $this->_configuration = new Payplug('abc','1970-01-01');
        Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
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
        $payment = Notification::treat($body, $this->_configuration);
        $this->assertTrue($payment instanceof Resource\Payment);
        $this->assertEquals('real_payment', $payment->id);
    }

    public function testTreatInstallmentPlan()
    {

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{ "id": "real_installment_plan", "object": "installment_plan" }'));
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

        $body = '{ "id": "inst_123456", "object": "installment_plan" }';
        $installmentPlan = Notification::treat($body, $this->_configuration);
        $this->assertTrue($installmentPlan instanceof Resource\InstallmentPlan);
        $this->assertEquals('real_installment_plan', $installmentPlan->id);
    }

    public function testTreatWhenBodyIsNotValidJSON()
    {
        $this->setExpectedException('\PayPlug\Exception\UnknownAPIResourceException');

        $this->_requestMock
            ->expects($this->never())
            ->method('exec');

        $body = 'invalidJSON';
        Notification::treat($body, $this->_configuration);
    }
}
