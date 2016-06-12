<?php
namespace Payplug;

class APIResourceMock extends Resource\APIResource
{
    static function fromAttributes(array $attributes)
    {
        $object = new APIResourceMock();
        $object->initialize($attributes);
        return $object;
    }
}

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class APIResourceTest extends \PHPUnit_Framework_TestCase
{
    private $_myApiResource = null;

    protected function setUp()
    {
        $this->_myApiResource = APIResourceMock::fromAttributes(array(
            'attr1' => 'val_attr1',
            'attr2' => 'val_attr2'
        ));
    }

    public function testThrowsExceptionWhenKeyDoesNotExist()
    {
        $this->setExpectedException('\PayPlug\Exception\UndefinedAttributeException');
        $this->_myApiResource->an_undefined_attribute;
    }

    public function testIssetReturnsTrueWhenPropertyExists()
    {
        $this->assertTrue(isset($this->_myApiResource->attr1));
    }

    public function testIssetReturnsFalseWhenPropertyDoesNotExist()
    {
        $this->assertFalse(isset($this->_myApiResource->an_undefined_attribute));
    }

    public function testPaymentFromAPIResourceFactory()
    {
        $attributes = array(
            'id'        =>  'pay_123',
            'object'    =>  'payment'
        );
        $payment = Resource\APIResource::factory($attributes);
        $this->assertTrue($payment instanceof Resource\Payment);
        $this->assertEquals('pay_123', $payment->id);
    }

    public function testRefundFromAPIResourceFactory()
    {
        $attributes = array(
            'id'        =>  're_123',
            'object'    =>  'refund'
        );
        $refund = Resource\APIResource::factory($attributes);
        $this->assertTrue($refund instanceof Resource\Refund);
        $this->assertEquals('re_123', $refund->id);
    }

    public function testAPIResourceFactoryWhenObjectIsNotDefined()
    {
        $this->setExpectedException('\PayPlug\Exception\UnknownAPIResourceException');
        $attributes = array(
            'id'    => 'a_random_object'
        );
        Resource\APIResource::factory($attributes);
    }

    public function testAPIResourceFactoryWhenObjectIsUnknown()
    {
        $this->setExpectedException('\PayPlug\Exception\UnknownAPIResourceException');
        $attributes = array(
            'id'        => 'a_random_object',
            'object'    => 'an_unknown_object'
        );
        Resource\APIResource::factory($attributes);
    }
}
