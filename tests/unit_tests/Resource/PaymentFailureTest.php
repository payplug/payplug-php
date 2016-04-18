<?php
namespace Payplug\Resource;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class PaymentPaymentFailureTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatePaymentFailureFromAttributes()
    {
        $paymentFailure = PaymentPaymentFailure::fromAttributes(array(
            'code'      => 'card_stolen',
            'message'   => 'The card is stolen.'
        ));

        $this->assertEquals('card_stolen', $paymentFailure->code);
        $this->assertEquals('The card is stolen.', $paymentFailure->message);
    }
}
