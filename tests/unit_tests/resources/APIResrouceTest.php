<?php

class APIResourceMock extends PayPlug_APIResource
{
    static function fromAttributes(array $attributes)
    {
        $object = new APIResourceMock();
        $object->initialize($attributes);
        return $object;
    }

    public function getAttributesWrapper()
    {
        return $this->getAttributes();
    }
}

/**
 * @group unit
 */
class APIResourceTest extends PHPUnit_Framework_TestCase
{
    private $_myApiResource = null;

    protected function setUp()
    {
        $this->_myApiResource = APIResourceMock::fromAttributes(array(
            'attr1' => 'val_attr1',
            'attr2' => 'val_attr2'
        ));
    }

    public function testCanGetAttributes()
    {
        $this->assertEquals(array(
            'attr1' => 'val_attr1',
            'attr2' => 'val_attr2'
        ), $this->_myApiResource->getAttributesWrapper());
    }

    public function testThrowsExceptionWhenKeyDoesNotExist()
    {
        $this->setExpectedException('PayPlug_UndefinedAttributeException');
        $this->_myApiResource->an_undefined_attribute;
    }
}
