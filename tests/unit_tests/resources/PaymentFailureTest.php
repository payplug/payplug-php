<?php

/**
 * @group unit
 */
class PaymentFailureTest extends PHPUnit_Framework_TestCase
{
    public function testCreatePaymentFailureFromAttributes()
    {
        $paymentFailure = PayPlug_PaymentFailure::fromAttributes(array(
            'code'      => 'card_stolen',
            'message'   => 'The card is stolen.'
        ));

        $this->assertEquals('card_stolen', $paymentFailure->code);
        $this->assertEquals('The card is stolen.', $paymentFailure->message);
    }
}
