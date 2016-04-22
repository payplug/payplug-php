<?php
namespace Payplug\Resource;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class PaymentCardTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateCardFromAttributes()
    {
        $card = PaymentCard::fromAttributes(array(
            'last4'     => '1234',
            'country'   => 'FR',
            'exp_year'  => 2022,
            'exp_month' => 12,
            'brand'     => 'Visa'
        ));

        $this->assertEquals('1234', $card->last4);
        $this->assertEquals('FR', $card->country);
        $this->assertEquals(2022, $card->exp_year);
        $this->assertEquals(12, $card->exp_month);
        $this->assertEquals('Visa', $card->brand);
    }
}
