<?php

require_once 'lib/PayPlug.php';

class RefundUnitTest extends PHPUnit_Framework_TestCase {
    public function testCreateRefundFromAttributes()
    {
        $refund = PayPlug_Refund::fromAttributes(array(
            'id'            => 're_390312',
            'payment_id'    => 'pay_490329',
            'object'        => 'refund',
            'amount'        => 3300,
            'currency'      => 'EUR',
            'created_at'    => 1410437760
        ));

        $this->assertEquals('re_390312', $refund->getAttribute('id'));
        $this->assertEquals('pay_490329', $refund->getAttribute('payment_id'));
        $this->assertEquals('refund', $refund->getAttribute('object'));
        $this->assertEquals(3300, $refund->getAttribute('amount'));
        $this->assertEquals('EUR', $refund->getAttribute('currency'));
        $this->assertEquals(1410437760, $refund->getAttribute('created_at'));
    }
}
