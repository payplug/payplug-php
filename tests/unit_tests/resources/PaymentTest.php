<?php

/**
 * @group unit
 * @group ci
 */
class PaymentTest extends PHPUnit_Framework_TestCase
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

    public function testCreatePaymentFromAttributes()
    {
        $payment = PayPlug_Payment::fromAttributes(array(
            'id'                => 'pay_490329',
            'object'            => 'payment',
            'is_live'           => true,
            'amount'            => 3300,
            'amount_refunded'   => 0,
            'currency'          => 'EUR',
            'created_at'        => 1410437760,
            'is_paid'           => true,
            'is_refunded'       => false,
            'is_3ds'            => false,
            'card'              => array(
                'last4'     => '1800',
                'country'   => 'FR',
                'exp_year'  => 2017,
                'exp_month' => 9,
                'brand'     => 'Mastercard'
            ),
            'customer'          => array(
                'email'         => 'name@customer.net',
                'first_name'    => 'John',
                'last_name'     => 'Doe'
            ),
            'hosted_payment'    => array(
                'payment_url'       => 'https://www.payplug.com/p/b9868d18546711e490c612314307c934',
                'ipn_url'           => 'http://yourwebsite.com/payplug_ipn',
                'return_url'        => 'http://yourwebsite.com/payplug_return?someid=11235',
                'cancel_url'        => 'http://yourwebsite.com/payplug_cancel?someid=81321',
                'paid_at'           => 1410437806,
                'ipn_answer_code'   => 200
            ),
            'failure'           => array(
                'code'      => null,
                'message'   => null
            ),
        ));

        $this->assertEquals('pay_490329', $payment->id);
        $this->assertEquals('payment', $payment->object);
        $this->assertEquals(true, $payment->is_live);
        $this->assertEquals(3300, $payment->amount);
        $this->assertEquals(0, $payment->amount_refunded);
        $this->assertEquals('EUR', $payment->currency);
        $this->assertEquals(1410437760, $payment->created_at);
        $this->assertEquals(true, $payment->is_paid);
        $this->assertEquals(false, $payment->is_refunded);
        $this->assertEquals(false, $payment->is_3ds);

        $this->assertEquals('1800', $payment->card->last4);
        $this->assertEquals('FR', $payment->card->country);
        $this->assertEquals(2017, $payment->card->exp_year);
        $this->assertEquals(9, $payment->card->exp_month);
        $this->assertEquals('Mastercard', $payment->card->brand);

        $this->assertEquals('name@customer.net', $payment->customer->email);
        $this->assertEquals('John', $payment->customer->first_name);
        $this->assertEquals('Doe', $payment->customer->last_name);

        $this->assertEquals('https://www.payplug.com/p/b9868d18546711e490c612314307c934', $payment->hosted_payment->payment_url);
        $this->assertEquals('http://yourwebsite.com/payplug_ipn', $payment->hosted_payment->ipn_url);
        $this->assertEquals('http://yourwebsite.com/payplug_return?someid=11235', $payment->hosted_payment->return_url);
        $this->assertEquals('http://yourwebsite.com/payplug_cancel?someid=81321', $payment->hosted_payment->cancel_url);
        $this->assertEquals(1410437806, $payment->hosted_payment->paid_at);
        $this->assertEquals(200, $payment->hosted_payment->ipn_answer_code);

        $this->assertNull($payment->failure->code);
        $this->assertNull($payment->failure->message);
    }

    public function testPaymentCreate()
    {
        function testPaymentCreate_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        // Anonymous functions not available with PHP 5.2 :(
        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = null;
        function testPaymentCreate_setopt($option, $value = null) {
            switch($option) {
                case CURLOPT_POSTFIELDS:
                    $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = json_decode($value, true);
                    return true;
            }
            return true;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));

        $this->_requestMock
            ->expects($this->atLeastOnce())
            ->method('setopt')
            ->will($this->returnCallback('testPaymentCreate_setopt'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testPaymentCreate_getinfo'));

        $payment = PayPlug_Payment::create(array(
            'amount'            => 999,
            'currency'          => 'EUR',
            'customer'          => array(
                'email'         => 'john.doe@example.com',
                'first_name'    => 'John',
                'last_name'     => 'Doe'
            ),
            'hosted_payment'    => array(
                'notification_url'  => 'http://www.example.org/callbackURL',
                'return_url'        => 'https://www.example.com/thank_you_for_your_payment.html',
                'cancel_url'        => 'https://www.example.com/so_bad_it_didnt_make_it.html'
            ),
            'force_3ds'         => false
        ));

        $this->assertTrue(is_array($GLOBALS['CURLOPT_POSTFIELDS_DATA']));
        $this->assertEquals('ok', $payment->status);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testPaymentRetrieve()
    {
        function testPaymentRetrieve_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        // Anonymous functions not available with PHP 5.2 :(
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testPaymentRetrieve_setopt($option, $value = null) {
            switch($option) {
                case CURLOPT_URL:
                    $GLOBALS['CURLOPT_URL_DATA'] = $value;
                    return true;
            }
            return true;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testPaymentRetrieve_getinfo'));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback('testPaymentRetrieve_setopt'));

        $payment = PayPlug_Payment::retrieve('a_payment_id');

        $this->assertStringEndsWith('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertEquals('ok', $payment->status);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testPaymentRefundWhenPaymentIsInvalid()
    {
        $this->setExpectedException('PayPlug_InvalidPaymentException');

        $payment = PayPlug_Payment::fromAttributes(array('fake' => 'payment'));
        $payment->refund(array('amount' => 3300));
    }

    public function testPaymentRefund()
    {
        function testPaymentRefund_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        // Anonymous functions not available with PHP 5.2 :(
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testPaymentRefund_setopt($option, $value = null) {
            switch($option) {
                case CURLOPT_URL:
                    $GLOBALS['CURLOPT_URL_DATA'] = $value;
                    return true;
            }
            return true;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testPaymentRefund_getinfo'));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback('testPaymentRefund_setopt'));

        $payment = PayPlug_Payment::fromAttributes(array('id' => 'a_payment_id'));
        $payment->refund(array('amount' => 3300));

        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }
}
