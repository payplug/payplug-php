<?php

require_once 'lib/PayPlug.php';

class PaymentTest extends \PHPUnit_Framework_TestCase {
    protected $_configuration;

    protected function setUp()
    {
        $this->_configuration = new PayPlug_ClientConfiguration(TestsConfig::LIVE_TOKEN, TestsConfig::TEST_TOKEN, true);
    }

    public function testCanCreatePayment()
    {
        $payment = PayPlug_Payment::create(array(
            'amount'            => 4200,
            'currency'          => 'EUR',
            'customer'          => array(
                'email'         => 'nleroux@payplug.com',
                'first_name'    => 'John',
                'last_name'     => 'Doe'
            ),
            'hosted_payment'    => array(
                'notification_url'  => 'https://www.payplug.com/?notification',
                'return_url'        => 'https://www.payplug.com/?return',
                'cancel_url'        => 'https://www.payplug.com/?cancel'
            ),
            'force_3ds'         => false
        ), $this->_configuration);

        $this->assertNotEmpty($payment->getAttribute('id'));
        $this->assertFalse($payment->getAttribute('is_live'));
        $this->assertEquals(4200, $payment->getAttribute('amount'));
        $this->assertEquals('nleroux@payplug.com', $payment->getAttribute('customer')->getAttribute('email'));
        $this->assertEquals('John', $payment->getAttribute('customer')->getAttribute('first_name'));
        $this->assertEquals('Doe', $payment->getAttribute('customer')->getAttribute('last_name'));
        $this->assertEquals('https://www.payplug.com/?notification', $payment->getAttribute('hosted_payment')->getAttribute('notification_url'));
        $this->assertEquals('https://www.payplug.com/?return', $payment->getAttribute('hosted_payment')->getAttribute('return_url'));
        $this->assertEquals('https://www.payplug.com/?cancel', $payment->getAttribute('hosted_payment')->getAttribute('cancel_url'));
    }

    public function testCanRetrieveAPayment()
    {
        $payment = PayPlug_Payment::create(array(
            'amount'            => 4200,
            'currency'          => 'EUR',
            'customer'          => array(
                'email'         => 'nleroux@payplug.com',
                'first_name'    => 'John',
                'last_name'     => 'Doe'
            ),
            'hosted_payment'    => array(
                'notification_url'  => 'https://www.payplug.com/',
                'return_url'        => 'https://www.payplug.com/',
                'cancel_url'        => 'https://www.payplug.com/'
            ),
            'force_3ds'         => false
        ), $this->_configuration);
        $getPayment = PayPlug_Payment::retrieve($payment->getAttribute('id'), $this->_configuration);

        $this->assertEquals($getPayment->getAttribute('id'), $payment->getAttribute('id'));
        $this->assertEquals(
            $getPayment->getAttribute('customer')->getAttribute('email'),
            $payment->getAttribute('customer')->getAttribute('email')
        );
    }
}
