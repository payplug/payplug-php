<?php
namespace Payplug\Resource;
use Payplug;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class CardTest extends \PHPUnit_Framework_TestCase
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

    public function testCreateCardFromAttributes()
    {
        $card = Card::fromAttributes(array(
            'id'            => 'card_167oJVCpvtR9j8N85LraL2GA',
            'object'        => 'card',
            'created_at'    => 1431523049,
            'is_live'       => false,
            'last4'         => '1111',
            'brand'         => 'Visa',
            'exp_month'     => 5,
            'exp_year'      => 2019,
            'customer_id'   => 'cus_6ESfofiMiLBjC6',
            'country'       => 'France',
            'metadata'      => array(
                'a_custom_field'    => 'a custom value',
                'another_key'       => 'another value'
            )
        ));

        $this->assertEquals('card_167oJVCpvtR9j8N85LraL2GA', $card->id);
        $this->assertEquals('card', $card->object);
        $this->assertEquals(1431523049, $card->created_at);
        $this->assertEquals(false, $card->is_live);
        $this->assertEquals('1111', $card->last4);
        $this->assertEquals('Visa', $card->brand);
        $this->assertEquals(5, $card->exp_month);
        $this->assertEquals(2019, $card->exp_year);
        $this->assertEquals('cus_6ESfofiMiLBjC6', $card->customer_id);
        $this->assertEquals('France', $card->country);

        $this->assertEquals('a custom value', $card->metadata['a_custom_field']);
        $this->assertEquals('another value', $card->metadata['another_key']);
    }

    public function testCardCreateFromCustomerId()
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

        $card = Payplug\Card::create('some_customer_id', array(
            'card' => 'tok_e34rfkljlkfje'
        ));

        $this->assertTrue(is_array($GLOBALS['CURLOPT_POSTFIELDS_DATA']));
        $this->assertEquals('ok', $card->status);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testCardCreateFromCustomerObject()
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

        $customer = Customer::fromAttributes(array('id' => 'some_customer_id'));
        $card = Payplug\Card::create($customer, array(
            'card' => 'tok_e34rfkljlkfje'
        ));

        $this->assertTrue(is_array($GLOBALS['CURLOPT_POSTFIELDS_DATA']));
        $this->assertEquals('ok', $card->status);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testCardRetrieveFromCustomerId()
    {
        function testCardRetrieveFromCustomerId_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testCardRetrieveFromCustomerId_setopt($option, $value = null) {
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

        $card = Payplug\Card::retrieve('a_customer_id', 'a_card_id');

        $this->assertEquals('ok', $card->status);
        $this->assertContains('a_customer_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertStringEndsWith('a_card_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testCardRetrieveFromCustomerObject()
    {
        function testCardRetrieveFromCustomerObject_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testCardRetrieveFromCustomerObject_setopt($option, $value = null) {
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

        $customer = Customer::fromAttributes(array('id' => 'a_customer_id'));
        $card = Payplug\Card::retrieve($customer, 'a_card_id');

        $this->assertEquals('ok', $card->status);
        $this->assertContains('a_customer_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertStringEndsWith('a_card_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testCardDeleteFromCustomerId()
    {
        function testCardDeleteFromCustomerId_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testCardDeleteFromCustomerId_setopt($option, $value = null) {
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

        Payplug\Card::delete('a_customer_id', 'a_card_id');

        $this->assertContains('a_customer_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertStringEndsWith('a_card_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testCardDeleteFromCustomerObject()
    {
        function testCardDeleteFromCustomerObject_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testCardDeleteFromCustomerObject_setopt($option, $value = null) {
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

        $customer = Customer::fromAttributes(array('id' => 'a_customer_id'));
        $card = Card::fromAttributes(array('id' => 'a_card_id'));
        Payplug\Card::delete($customer, $card);

        $this->assertContains('a_customer_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertStringEndsWith('a_card_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testCardDeleteCardObject()
    {
        function testCardDeleteCardObject_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }
        $GLOBALS['CURLOPT_URL_DATA'] = null;
        function testCardDeleteCardObject_setopt($option, $value = null) {
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

        $card = Card::fromAttributes(array('id' => 'a_card_id', 'customer_id' => 'a_customer_id'));
        $card->delete();

        $this->assertContains('a_customer_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertStringEndsWith('a_card_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testCardsListThrowsExceptionOnWongAPIResponse()
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

        Payplug\Card::listCards('a_customer_id');
    }

    public function testCardsListFromCustomerId()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"data":[{"id": "card1"}, {"id": "card2"}]}'));

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

        $cards = Payplug\Card::listCards('a_customer_id');

        $this->assertContains('a_customer_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertEquals(2, count($cards));
        $this->assertTrue('card1' === $cards[0]->id || 'card2' === $cards[1]->id);
        $this->assertTrue(
            (('card1' === $cards[1]->id) || ('card2' === $cards[1]->id))
            && ($cards[0]->id !== $cards[1]->id)
        );

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testCardsListFromCustomerObject()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"data":[{"id": "card1"}, {"id": "card2"}]}'));

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

        $cards = Payplug\Card::listCards(
            Customer::fromAttributes(array('id' => 'a_customer_id'))
        );

        $this->assertContains('a_customer_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertEquals(2, count($cards));
        $this->assertTrue('card1' === $cards[0]->id || 'card2' === $cards[1]->id);
        $this->assertTrue(
            (('card1' === $cards[1]->id) || ('card2' === $cards[1]->id))
            && ($cards[0]->id !== $cards[1]->id)
        );

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }
}
