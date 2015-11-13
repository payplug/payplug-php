<?php
namespace Payplug\Exception;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class PayplugExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $exception = $this->getMockForAbstractClass('\PayPlug\Exception\HttpException', array('this_is_a_message', 808));

        $this->assertContains('Mock_HttpException', (string)$exception);
        $this->assertContains('this_is_a_message', (string)$exception);
        $this->assertContains('808', (string)$exception);
    }
}