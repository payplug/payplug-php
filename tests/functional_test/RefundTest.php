<?php

/**
 * @group functional
 */
class RefundFunctionalTest extends PHPUnit_Framework_TestCase
{
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

        fwrite(STDOUT, "\nPay this test payment and press enter: " . $payment->hosted_payment->payment_url);
        fgets(fopen("php://stdin", "r"));

        $refund = $payment->refund(array(
            'amount'    => 100,
        ), $this->_configuration);

        $this->assertEquals($payment->id, $refund->payment_id);

        $refund = $payment->refund(array(
            'amount'    => 200,
        ), $this->_configuration);

        $this->assertEquals($payment->id, $refund->payment_id);

        return $payment;
    }

    /**
     * @depends testCanRefundAPayment
     */
    public function testCanListRefunds(PayPlug_Payment $payment)
    {
        $refunds = $payment->listRefunds($this->_configuration);
        $this->assertEquals(2, count($refunds));
        $this->assertTrue(($refunds[0]->amount === 100) || ($refunds[0]->amount === 200));
        $this->assertTrue(($refunds[1]->amount === 100) || ($refunds[1]->amount === 200));
        $this->assertFalse($refunds[0]->amount === $refunds[1]->amount);
    }
}
