<?php
namespace Payplug\Resource;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class PaymentTest extends \PHPUnit_Framework_TestCase
{
    private $_requestMock;
    private $_configuration;

    protected function setUp()
    {
        $this->_configuration = new \Payplug\Payplug('abc');
        \Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        \Payplug\Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    public function testCreatePaymentFromAttributes()
    {
        $payment = \Payplug\Resource\Payment::fromAttributes(array(
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
                'last4'             => '1800',
                'country'           => 'FR',
                'exp_year'          => 2017,
                'exp_month'         => 9,
                'brand'             => 'Mastercard'
            ),
            'customer'          => array(
                'email'             => 'name@customer.net',
                'first_name'        => 'John',
                'last_name'         => 'Doe'
            ),
            'hosted_payment'    => array(
                'payment_url'       => 'https://www.payplug.com/p/b9868d18546711e490c612314307c934',
                'return_url'        => 'http://yourwebsite.com/payplug_return?someid=11235',
                'cancel_url'        => 'http://yourwebsite.com/payplug_cancel?someid=81321',
                'paid_at'           => 1410437806
            ),
            'notification'      => array(
                'url'               => 'http://yourwebsite.com/payplug_ipn',
                'response_code'     => 200
            ),
            'failure'           => array(
                'code'              => null,
                'message'           => null
            ),
            'metadata'          => array(
                'a_custom_field'    => 'a custom value',
                'another_key'       => 'another value'
            )
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

        $this->assertEquals('http://yourwebsite.com/payplug_return?someid=11235', $payment->hosted_payment->return_url);
        $this->assertEquals('http://yourwebsite.com/payplug_cancel?someid=81321', $payment->hosted_payment->cancel_url);
        $this->assertEquals(1410437806, $payment->hosted_payment->paid_at);
        $this->assertEquals('http://yourwebsite.com/payplug_ipn', $payment->notification->url);
        $this->assertEquals(200, $payment->notification->response_code);

        $this->assertNull($payment->failure->code);
        $this->assertNull($payment->failure->message);

        $this->assertEquals('a custom value', $payment->metadata['a_custom_field']);
        $this->assertEquals('another value', $payment->metadata['another_key']);
    }

    public function testPaymentCreate()
    {
        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));

        $this->_requestMock
            ->expects($this->atLeastOnce())
            ->method('setopt')
            ->will($this->returnCallback(function($option, $value = null) {
                switch($option) {
                    case CURLOPT_POSTFIELDS:
                        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = json_decode($value, true);
                        return true;
                }
                return true;
            }));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $payment = \Payplug\Resource\Payment::create(array(
            'amount'            => 999,
            'currency'          => 'EUR',
            'customer'          => array(
                'email'         => 'john.doe@example.com',
                'first_name'    => 'John',
                'last_name'     => 'Doe'
            ),
            'hosted_payment'    => array(
                'return_url'        => 'https://www.example.com/thank_you_for_your_payment.html',
                'cancel_url'        => 'https://www.example.com/so_bad_it_didnt_make_it.html'
            ),
            'notification_url'  => 'http://www.example.org/callbackURL',
            'force_3ds'         => false
        ));

        $this->assertTrue(is_array($GLOBALS['CURLOPT_POSTFIELDS_DATA']));
        $this->assertEquals('ok', $payment->status);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testPaymentRetrieve()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback(function($option, $value = null) {
                switch($option) {
                    case CURLOPT_URL:
                        $GLOBALS['CURLOPT_URL_DATA'] = $value;
                        return true;
                }
                return true;
            }));

        $payment = \Payplug\Resource\Payment::retrieve('a_payment_id');

        $this->assertStringEndsWith('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertEquals('ok', $payment->status);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testPaymentList()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"data":[{"id": "payment1"}, {"id": "payment2"}]}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $result = \Payplug\Resource\Payment::listPayments();
        $payments = $result['data'];
        $this->assertEquals(2, count($payments));
        $this->assertTrue($payments[0]->id === 'payment1' || $payments[0]->id === 'payment2');
        $this->assertTrue($payments[1]->id === 'payment1' || $payments[1]->id === 'payment2');
        $this->assertTrue($payments[0]->id !== $payments[1]->id);


        unset($GLOBALS['CURLOPT_URL_DATA']);
    }


    public function testPaymentPaginationList()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"per_page": 1, "page": 0, "data":[{"id": "payment1"}]}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $perPage = 1;
        $page = 0;
        $payments = \PayPlug\Resource\Payment::listPayments($perPage, $page);

        $this->assertEquals($payments['per_page'], 1);
        $this->assertEquals($payments['page'], 0);
        $payments = $payments['data'];
        $this->assertEquals(1, count($payments));
        $this->assertTrue($payments[0]->id == 'payment1');


        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testPaymentRefundWhenPaymentIsInvalid()
    {
        $this->setExpectedException('\PayPlug\Exception\InvalidPaymentException');

        $payment = \Payplug\Resource\Payment::fromAttributes(array('fake' => 'payment'));
        $payment->refund(array('amount' => 3300));
    }

    public function testPaymentRefund()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback(function($option, $value = null) {
                switch($option) {
                    case CURLOPT_URL:
                        $GLOBALS['CURLOPT_URL_DATA'] = $value;
                        return true;
                }
                return true;
            }));

        $payment = \Payplug\Resource\Payment::fromAttributes(array('id' => 'a_payment_id'));
        $payment->refund(array('amount' => 3300));

        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testPaymentListRefundsWhenPaymentIsInvalid()
    {
        $this->setExpectedException('\PayPlug\Exception\InvalidPaymentException');

        $payment = \Payplug\Resource\Payment::fromAttributes(array('fake' => 'payment'));
        $payment->listRefunds();
    }

    public function testPaymentListRefunds()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"data":[{"id": "refund1"}, {"id": "refund2"}]}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback(function($option, $value = null) {
                switch($option) {
                    case CURLOPT_URL:
                        $GLOBALS['CURLOPT_URL_DATA'] = $value;
                        return true;
                }
                return true;
            }));

        $payment = \Payplug\Resource\Payment::fromAttributes(array('id' => 'a_payment_id'));
        $result = $payment->listRefunds();
        $refunds = $result['data'];

        $this->assertEquals(2, count($refunds));
        $this->assertTrue($refunds[0]->id === 'refund1' || $refunds[0]->id === 'refund2');
        $this->assertTrue($refunds[1]->id === 'refund1' || $refunds[1]->id === 'refund2');
        $this->assertTrue($refunds[0]->id !== $refunds[1]->id);

        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRetrieveConsistentPaymentWhenIdIsUndefined()
    {
        $this->setExpectedException('\PayPlug\Exception\UndefinedAttributeException');

        $payment = \Payplug\Resource\Payment::fromAttributes(array('this_payment' => 'has_no_id'));
        $payment->getConsistentResource();
    }

    public function testRetrieveConsistentPayment()
    {
        function testRetrieveConsistentPayment_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"id": "pay_345"}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnValue(true));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $payment1 = \Payplug\Resource\Payment::fromAttributes(array('id' => 'pay_123'));
        $payment2 = $payment1->getConsistentResource($this->_configuration);

        $this->assertEquals('pay_123', $payment1->id);
        $this->assertEquals('pay_345', $payment2->id);
    }
}
