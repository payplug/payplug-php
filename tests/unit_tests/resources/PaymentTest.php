<?php

require_once 'lib/PayPlug.php';

/**
 * @group unit
 */
class PaymentTest extends PHPUnit_Framework_TestCase
{
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
}
