<?php
namespace Payplug\Resource;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class PaymentAuthorizationTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateAuthorizationFromAttributes()
    {
        $hostedPayment = PaymentHostedPayment::fromAttributes(array(
            'authorized_at'     => 1554896133,
            'expires_at'        => 1555459200,
            'authorized_amount' => 4200,
        ));

        $this->assertEquals(1554896133, $hostedPayment->authorized_at);
        $this->assertEquals(1555459200, $hostedPayment->expires_at);
        $this->assertEquals(4200, $hostedPayment->authorized_amount);
    }
}
