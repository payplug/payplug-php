<?php
namespace Payplug\Resource;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class PaymentHostedPaymentTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateHostedPaymentFromAttributes()
    {
        $hostedPayment = PaymentHostedPayment::fromAttributes(array(
            'payment_url'       => 'https://www.payplug.com/pay/test/7ZcMGi6KNnVT5H7o9hms9g',
            'notification_url'  => 'https://www.payplug.com/?notification',
            'return_url'        => 'https://www.payplug.com/?return',
            'cancel_url'        => 'https://www.payplug.com/?cancel',
            'paid_at'           => 1410437806,
            'notification_answer_code'   => 200,
        ));

        $this->assertEquals('https://www.payplug.com/pay/test/7ZcMGi6KNnVT5H7o9hms9g', $hostedPayment->payment_url);
        $this->assertEquals('https://www.payplug.com/?notification', $hostedPayment->notification_url);
        $this->assertEquals('https://www.payplug.com/?return', $hostedPayment->return_url);
        $this->assertEquals('https://www.payplug.com/?cancel', $hostedPayment->cancel_url);
        $this->assertEquals(1410437806, $hostedPayment->paid_at);
        $this->assertEquals(200, $hostedPayment->notification_answer_code);
    }
}
