<?php

/**
 * @group unit
 * @group ci
 */
class RefundTest extends PHPUnit_Framework_TestCase
{
    private $_requestMock;
    private $_configuration;

    protected function setUp()
    {
        $this->_configuration = new PayPlug_ClientConfiguration('abc', 'cba', true);
        PayPlug_ClientConfiguration::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('PayPlug_IHttpRequest');
        PayPlug_HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

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

    public function testRefundCreateFromPaymentId()
    {
        function testRefundCreateFromPaymentId_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        // Anonymous functions not available with PHP 5.2 :(
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testRefundCreateFromPaymentId_setopt($option, $value = null) {
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
            ->will($this->returnCallback('testRefundCreateFromPaymentId_getinfo'));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback('testRefundCreateFromPaymentId_setopt'));

        $refund = PayPlug_Refund::create('a_payment_id', array('amount' => 3300));

        $this->assertEquals('ok', $refund->status);
        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRefundCreateFromPaymentObject()
    {
        function testRefundCreateFromPaymentObject_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        // Anonymous functions not available with PHP 5.2 :(
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testRefundCreateFromPaymentObject_setopt($option, $value = null) {
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
            ->will($this->returnCallback('testRefundCreateFromPaymentObject_getinfo'));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback('testRefundCreateFromPaymentObject_setopt'));

        $refund = PayPlug_Refund::create(
            PayPlug_Payment::fromAttributes(array('id' => 'a_payment_id')),
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

        // Anonymous functions not available with PHP 5.2 :(
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
            ->will($this->returnCallback('testRefundCreateFromPaymentId_getinfo'));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback('testRefundCreateFromPaymentId_setopt'));

        $refund = PayPlug_Refund::retrieve('a_payment_id', 'a_refund_id');

        $this->assertEquals('ok', $refund->status);
        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertStringEndsWith('a_refund_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRefundRetrieveFromPaymentObject()
    {
        function testRefundRetrieveFromPaymentObject_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        // Anonymous functions not available with PHP 5.2 :(
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testRefundRetrieveFromPaymentObject_setopt($option, $value = null) {
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
            ->will($this->returnCallback('testRefundRetrieveFromPaymentObject_getinfo'));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback('testRefundRetrieveFromPaymentObject_setopt'));

        $refund = PayPlug_Refund::retrieve(
            PayPlug_Payment::fromAttributes(array('id' => 'a_payment_id')),
            'a_refund_id'
        );

        $this->assertEquals('ok', $refund->status);
        $this->assertContains('a_payment_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertStringEndsWith('a_refund_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRefundsListThrowsExceptionOnWongAPIResponse()
    {
        $this->setExpectedException('PayPlug_UnexpectedAPIResponseException');

        function testRefundsListThrowsExceptionOnWongAPIResponse_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"this_is_an_invalid_response"}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testRefundsListThrowsExceptionOnWongAPIResponse_getinfo'));

        PayPlug_Refund::list_refunds('a_payment_id');
    }

    public function testRefundsListFromPaymentId()
    {
        function testRefundsListFromPaymentId_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        // Anonymous functions not available with PHP 5.2 :(
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testRefundsListFromPaymentId_setopt($option, $value = null) {
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
            ->will($this->returnValue('{"data":[{"id": "refund1"}, {"id": "refund2"}]}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testRefundsListFromPaymentId_getinfo'));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback('testRefundsListFromPaymentId_setopt'));

        $refunds = PayPlug_Refund::list_refunds('a_payment_id');

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
        function testRefundsListFromPaymentObject_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        // Anonymous functions not available with PHP 5.2 :(
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testRefundsListFromPaymentObject_setopt($option, $value = null) {
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
            ->will($this->returnValue('{"data":[{"id": "refund1"}, {"id": "refund2"}]}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testRefundRetrieveFromPaymentObject_getinfo'));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback('testRefundRetrieveFromPaymentObject_setopt'));

        $refunds = PayPlug_Refund::list_refunds(
            PayPlug_Payment::fromAttributes(array('id' => 'a_payment_id'))
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
}
