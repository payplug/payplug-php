<?php

require_once 'lib/PayPlug.php';

/**
 * @group functional
 */
class PaymentFunctionalTest extends PHPUnit_Framework_TestCase
{
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

        $this->assertNotEmpty($payment->id);
        $this->assertFalse($payment->is_live);
        $this->assertEquals(4200, $payment->amount);
        $this->assertEquals('nleroux@payplug.com', $payment->customer->email);
        $this->assertEquals('John', $payment->customer->first_name);
        $this->assertEquals('Doe', $payment->customer->last_name);
        $this->assertEquals('https://www.payplug.com/?notification', $payment->hosted_payment->notification_url);
        $this->assertEquals('https://www.payplug.com/?return', $payment->hosted_payment->return_url);
        $this->assertEquals('https://www.payplug.com/?cancel', $payment->hosted_payment->cancel_url);
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
        $getPayment = PayPlug_Payment::retrieve($payment->id, $this->_configuration);

        $this->assertEquals($getPayment->id, $payment->id);
        $this->assertEquals(
            $getPayment->customer->email,
            $payment->customer->email
        );
    }
}
