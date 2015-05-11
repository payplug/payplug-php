<?php

require_once 'lib/PayPlug.php';

class CardUnitTest extends PHPUnit_Framework_TestCase {
    public function testCreateCardFromAttributes()
    {
        $card = PayPlug_Card::fromAttributes(array(
            'last4'     => '1234',
            'country'   => 'FR',
            'exp_year'  => 2022,
            'exp_month' => 12,
            'brand'     => 'Visa'
        ));

        $this->assertEquals('1234', $card->getAttribute('last4'));
        $this->assertEquals('FR',   $card->getAttribute('country'));
        $this->assertEquals(2022, $card->getAttribute('exp_year'));
        $this->assertEquals(12,   $card->getAttribute('exp_month'));
        $this->assertEquals('Visa', $card->getAttribute('brand'));
    }
}
