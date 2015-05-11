<?php

require_once 'lib/PayPlug.php';

class RefundFunctionalTest extends \PHPUnit_Framework_TestCase {
    protected $_configuration;

    protected function setUp()
    {
        $this->_configuration = new PayPlug_ClientConfiguration(TestsConfig::LIVE_TOKEN, TestsConfig::TEST_TOKEN, true);
    }

    public function testCanRefundAPayment()
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

        fwrite(STDOUT, "Please pay the test payment and press enter: " . $payment->getAttribute('hosted_payment')->getAttribute('payment_url'));
        fgets(fopen("php://stdin", "r"));

        $refund = $payment->refund(array(
            'amount'    => 100,
        ), $this->_configuration);

        $this->assertEquals($payment->getAttribute('id'), $refund->getAttribute('payment_id'));
    }
}
