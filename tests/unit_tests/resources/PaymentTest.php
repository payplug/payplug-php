<?php

require_once 'lib/PayPlug.php';

class PaymentUnitTest extends PHPUnit_Framework_TestCase {
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

        $this->assertEquals('pay_490329', $payment->getAttribute('id'));
        $this->assertEquals('payment', $payment->getAttribute('object'));
        $this->assertEquals(true, $payment->getAttribute('is_live'));
        $this->assertEquals(3300, $payment->getAttribute('amount'));
        $this->assertEquals(0, $payment->getAttribute('amount_refunded'));
        $this->assertEquals('EUR', $payment->getAttribute('currency'));
        $this->assertEquals(1410437760, $payment->getAttribute('created_at'));
        $this->assertEquals(true, $payment->getAttribute('is_paid'));
        $this->assertEquals(false, $payment->getAttribute('is_refunded'));
        $this->assertEquals(false, $payment->getAttribute('is_3ds'));

        $this->assertEquals('1800', $payment->getAttribute('card')->getAttribute('last4'));
        $this->assertEquals('FR', $payment->getAttribute('card')->getAttribute('country'));
        $this->assertEquals(2017, $payment->getAttribute('card')->getAttribute('exp_year'));
        $this->assertEquals(9, $payment->getAttribute('card')->getAttribute('exp_month'));
        $this->assertEquals('Mastercard', $payment->getAttribute('card')->getAttribute('brand'));

        $this->assertEquals('name@customer.net', $payment->getAttribute('customer')->getAttribute('email'));
        $this->assertEquals('John', $payment->getAttribute('customer')->getAttribute('first_name'));
        $this->assertEquals('Doe', $payment->getAttribute('customer')->getAttribute('last_name'));

        $this->assertEquals('https://www.payplug.com/p/b9868d18546711e490c612314307c934', $payment->getAttribute('hosted_payment')->getAttribute('payment_url'));
        $this->assertEquals('http://yourwebsite.com/payplug_ipn', $payment->getAttribute('hosted_payment')->getAttribute('ipn_url'));
        $this->assertEquals('http://yourwebsite.com/payplug_return?someid=11235', $payment->getAttribute('hosted_payment')->getAttribute('return_url'));
        $this->assertEquals('http://yourwebsite.com/payplug_cancel?someid=81321', $payment->getAttribute('hosted_payment')->getAttribute('cancel_url'));
        $this->assertEquals(1410437806, $payment->getAttribute('hosted_payment')->getAttribute('paid_at'));
        $this->assertEquals(200, $payment->getAttribute('hosted_payment')->getAttribute('ipn_answer_code'));

        $this->assertNull($payment->getAttribute('failure')->getAttribute('code'));
        $this->assertNull($payment->getAttribute('failure')->getAttribute('message'));
    }
}
