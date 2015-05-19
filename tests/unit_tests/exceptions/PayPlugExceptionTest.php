<?php

/**
 * @group unit
 * @group ci
 */
class PayPlugExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $exception = $this->getMockForAbstractClass('PayPlug_PayPlugException', array('this_is_a_message', 808));
        $this->assertContains('PayPlug_PayPlugException', (string)$exception);
        $this->assertContains('this_is_a_message', (string)$exception);
        $this->assertContains('808', (string)$exception);
    }
}