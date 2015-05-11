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

        $this->assertEquals('re_390312', $refund->id);
        $this->assertEquals('pay_490329', $refund->payment_id);
        $this->assertEquals('refund', $refund->object);
        $this->assertEquals(3300, $refund->amount);
        $this->assertEquals('EUR', $refund->currency);
        $this->assertEquals(1410437760, $refund->created_at);
    }
}
