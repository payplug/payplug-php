<?php

/**
* @group unit
* @group ci
* @group recommended
*/
class NotificationTest extends PHPUnit_Framework_TestCase
{

    private $_requestMock;
    private $_configuration;

    protected function setUp()
    {
        $this->_configuration = new PayPlug_ClientConfiguration('abc', 'cba', true);
        PayPlug_ClientConfiguration::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('PayPlug_IHttpRequest');
        PayPlug_HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    public function testTreatPayment()
    {
        function testTreatPayment_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{ "id": "real_payment", "object": "payment" }'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testTreatPayment_getinfo'));

        $body = '{ "id": "pay_123", "object": "payment" }';
        $payment = PayPlug_Notification::treat($body, $this->_configuration);
        $this->assertTrue($payment instanceof $payment);
        $this->assertEquals('real_payment', $payment->id);
    }

    public function testTreatWhenBodyIsNotValidJSON()
    {
        $this->setExpectedException('PayPlug_UnknownAPIResourceException');

        $this->_requestMock
            ->expects($this->never())
            ->method('exec');

        $body = 'invalidJSON';
        PayPlug_Notification::treat($body, $this->_configuration);
    }
}