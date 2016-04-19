<?php
namespace Payplug\Resource;
use Payplug;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class CustomerTest extends \PHPUnit_Framework_TestCase
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

    public function testCreateCustomerFromAttributes()
    {
        $customer = Customer::fromAttributes(array(
            'id'            => 'cus_6ESfofiMiLBjC6',
            'object'        => 'customer',
            'created_at'    => 1431523049,
            'is_live'       => false,
            'email'         => 'john.watson@example.net',
            'first_name'    => 'John',
            'last_name'     => 'Watson',
            'address1'      => '27 Rue Pasteur',
            'address2'      => null,
            'city'          => 'Paris',
            'postcode'      => '75018',
            'country'       => 'France',
            'metadata'      => array(
                'a_custom_field'    => 'a custom value',
                'another_key'       => 'another value'
            )
        ));

        $this->assertEquals('cus_6ESfofiMiLBjC6', $customer->id);
        $this->assertEquals('customer', $customer->object);
        $this->assertEquals(1431523049, $customer->created_at);
        $this->assertEquals(false, $customer->is_live);
        $this->assertEquals('john.watson@example.net', $customer->email);
        $this->assertEquals('John', $customer->first_name);
        $this->assertEquals('Watson', $customer->last_name);
        $this->assertEquals('27 Rue Pasteur', $customer->address1);
        $this->assertNull($customer->address2);
        $this->assertEquals('Paris', $customer->city);
        $this->assertEquals('75018', $customer->postcode);
        $this->assertEquals('France', $customer->country);

        $this->assertEquals('a custom value', $customer->metadata['a_custom_field']);
        $this->assertEquals('another value', $customer->metadata['another_key']);
    }

    public function testCustomerCreate()
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

        $customer = Customer::create(array(
            'email'         => 'john.watson@example.net',
            'first_name'    => 'John',
            'last_name'     => 'Watson',
            'address1'      => '27 Rue Pasteur',
            'address2'      => null,
            'city'          => 'Paris',
            'postcode'      => '75018',
            'country'       => 'France',
            'metadata'      => array(
                'a_custom_field'    => 'a custom value',
                'another_key'       => 'another value'
            )
        ));

        $this->assertTrue(is_array($GLOBALS['CURLOPT_POSTFIELDS_DATA']));
        $this->assertEquals('ok', $customer->status);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testCustomerUpdate()
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

        $customer = Payplug\Customer::update('cus_id', array('some' => 'updates'));

        $this->assertTrue(is_array($GLOBALS['CURLOPT_POSTFIELDS_DATA']));
        $this->assertEquals('ok', $customer->status);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testCustomerDelete()
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

        Payplug\Customer::delete('cus_id');

        $this->assertNull($GLOBALS['CURLOPT_POSTFIELDS_DATA']);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testCustomerRetrieve()
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

        $customer = Payplug\Customer::retrieve('cus_id');

        $this->assertNull($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
        $this->assertEquals('ok', $customer->status);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testCustomerList()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"data":[{"id": "cus1"}, {"id": "cus2"}]}'));

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

        $customers = Payplug\Customer::listCustomers();
        $this->assertEquals(2, count($customers));
        $this->assertTrue($customers[0]->id === 'cus1' || $customers[0]->id === 'cus2');
        $this->assertTrue($customers[1]->id === 'cus1' || $customers[1]->id === 'cus2');
        $this->assertTrue($customers[0]->id !== $customers[1]->id);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testCustomerPaginationList()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"per_page": 1, "page": 0, "data":[{"id": "cus1"}]}'));

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
        $customers = Payplug\Customer::listCustomers($perPage, $page);

        $this->assertEquals(1, count($customers));
        $this->assertTrue($customers[0]->id == 'cus1');

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testListCardsOfCustomer()
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

        $customer = Customer::fromAttributes(array('id' => 'a_customer_id'));
        $cards = $customer->listCards();

        $this->assertContains('a_customer_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertEquals(2, count($cards));
        $this->assertTrue('card1' === $cards[0]->id || 'card2' === $cards[1]->id);
        $this->assertTrue(
            (('card1' === $cards[1]->id) || ('card2' === $cards[1]->id))
            && ($cards[0]->id !== $cards[1]->id)
        );

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testAddCardToCustomer()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"id": "card1"}'));

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
        $card = $customer->addCard(array('some' => 'creation_data'));

        $this->assertContains('a_customer_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertEquals('card1', $card->id);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }
}
