<?php
namespace Payplug\Test;

/**
 * Gather some testing utilities.
 **/
class TestUtils
{
    /**
     * Call protected/private method of a class using reflexion.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed The call return value.
     */
    public static function invokePrivateMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
};
