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

    protected function setUpTwice()
    {
        $this->_configuration = new Payplug\Payplug('abc','1970-01-01');
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
            'exp_year'      => 2029,
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
        $this->assertEquals(2029, $card->exp_year);
        $this->assertEquals('cus_6ESfofiMiLBjC6', $card->customer_id);
        $this->assertEquals('France', $card->country);

        $this->assertEquals('a custom value', $card->metadata['a_custom_field']);
        $this->assertEquals('another value', $card->metadata['another_key']);
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

        $card = Card::fromAttributes(array('id' => 'a_card_id'));
        $card->delete();

        $this->assertStringEndsWith('a_card_id', $GLOBALS['CURLOPT_URL_DATA']);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }
}
