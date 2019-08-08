<?php

namespace Payplug;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class PayplugTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInitializeConfiguration()
    {
        Payplug::init(array(
            'secretKey' => 'cba',
            'apiVersion' => null
        ));

        $configuration = Payplug::getDefaultConfiguration();

        $this->assertEquals('cba', $configuration->getToken());
    }

    public function testCannotInitializeConfigurationWhenLiveTokenIsNotAString()
    {
        $this->setExpectedException('\PayPlug\Exception\ConfigurationException');
        Payplug::init(array(
            'secretKey' => true,
            'apiVersion' => '2019-06-14',
        ));
    }

    public function testCannotInitializeConfigurationWhenTestTokenIsArray()
    {
        $this->setExpectedException('\PayPlug\Exception\ConfigurationException');

        Payplug::init(array(
            'secretKey' => array(
                'LIVE_TOKEN' => 'cba'
            ),
            'apiVersion' => null,
        ));
    }

    public function testCanGetAToken()
    {
        $configuration = Payplug::init(array('secretKey' => 'cba', 'apiVersion' => null));
        $this->assertEquals('cba', $configuration->getToken());
    }

    /**
     * @runInSeparateProcess so that static default configuration is cleared before the test
     */
    public function testThrowsExceptionWhenDefaultConfigurationIsNotSet()
    {
        $this->setExpectedException('\Payplug\Exception\ConfigurationNotSetException');
        Payplug::getDefaultConfiguration();
    }

    public function testCanSetDefaultConfiguration()
    {
        $configuration = Payplug::init(array('secretKey' => 'abc', 'apiVersion' => null));
        Payplug::setDefaultConfiguration($configuration);
        $this->assertEquals($configuration, Payplug::getDefaultConfiguration());
    }
}
