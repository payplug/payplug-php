<?php
namespace Payplug\Resource;
use Payplug;
use Payplug\Core\HttpClient;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class RefundTest extends \PHPUnit_Framework_TestCase
{
    private $_requestMock;
    private $_configuration;

    protected function setUp()
    {
        $this->_configuration = new Payplug\Payplug('abc');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        Payplug\Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    protected function setUpTwice()
    {
        $this->_configuration = new Payplug\Payplug('abc','1970-01-01');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        Payplug\Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    public function testCreateRefundFromAttributes()
    {
        $refund = Refund::fromAttributes(array(
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

    public function testRefundCreateFromPaymentId()
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

        $refund = Refund::create('a_payment_id', array('amount' => 3300));

        $this->assertEquals('ok', $refund->status);
        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRefundCreateFromPaymentObject()
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

        $refund = Refund::create(
            Payment::fromAttributes(array('id' => 'a_payment_id')),
            array('amount' => 3300)
        );

        $this->assertEquals('ok', $refund->status);
        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRefundRetrieveFromPaymentId()
    {
        function testRefundRetrieveFromPaymentId_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testRefundRetrieveFromPaymentId_setopt($option, $value = null) {
            switch($option) {
                case CURLOPT_URL:
                    $GLOBALS['CURLOPT_URL_DATA'] = $value;
                    return true;
            }
            return true;
        }

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

        $refund = Refund::retrieve('a_payment_id', 'a_refund_id');

        $this->assertEquals('ok', $refund->status);
        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertStringEndsWith('a_refund_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRefundRetrieveFromPaymentObject()
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

        $refund = Refund::retrieve(
            Payment::fromAttributes(array('id' => 'a_payment_id')),
            'a_refund_id'
        );

        $this->assertEquals('ok', $refund->status);
        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertStringEndsWith('a_refund_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRefundsListThrowsExceptionOnWongAPIResponse()
    {
        $this->setExpectedException('\PayPlug\Exception\UnexpectedAPIResponseException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"this_is_an_invalid_response"}'));

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

        Refund::listRefunds('a_payment_id');
    }

    public function testRefundsListFromPaymentId()
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

        $refunds = Refund::listRefunds('a_payment_id');

        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertEquals(2, count($refunds));
        $this->assertTrue('refund1' === $refunds[0]->id || 'refund2' === $refunds[1]->id);
        $this->assertTrue(
            (('refund1' === $refunds[1]->id) || ('refund2' === $refunds[1]->id))
            && ($refunds[0]->id !== $refunds[1]->id)
        );

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRefundsListFromPaymentObject()
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

        $refunds = Refund::listRefunds(
            Payment::fromAttributes(array('id' => 'a_payment_id'))
        );

        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertEquals(2, count($refunds));
        $this->assertTrue('refund1' === $refunds[0]->id || 'refund2' === $refunds[1]->id);
        $this->assertTrue(
            (('refund1' === $refunds[1]->id) || ('refund2' === $refunds[1]->id))
            && ($refunds[0]->id !== $refunds[1]->id)
        );

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRetrieveConsistentRefundWhenIdIsUndefined()
    {
        $this->setExpectedException('\PayPlug\Exception\UndefinedAttributeException');

        $payment = Refund::fromAttributes(array('this_refund' => 'has_no_id', 'payment_id' => 'pay_id'));
        $payment->getConsistentResource();
    }

    public function testRetrieveConsistentRefundWhenPaymentIdIsUndefined()
    {
        $this->setExpectedException('\PayPlug\Exception\UndefinedAttributeException');

        $payment = Refund::fromAttributes(array('id' => 'an_id', 'no_payment_id' => ''));
        $payment->getConsistentResource();
    }

    public function testRetrieveConsistentRefund()
    {
        function testRetrieveConsistentRefund_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"id": "re_345", "payment_id": "pay_789"}'));

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

        $refund1 = Refund::fromAttributes(array('id' => 're_123', 'payment_id' => 'pay_321'));
        $refund2 = $refund1->getConsistentResource($this->_configuration);

        $this->assertEquals('re_123', $refund1->id);
        $this->assertEquals('pay_321', $refund1->payment_id);
        $this->assertEquals('re_345', $refund2->id);
        $this->assertEquals('pay_789', $refund2->payment_id);
    }
}
