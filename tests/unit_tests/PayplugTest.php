<?php

namespace Payplug;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class PayplugTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInitializeDefaultConfiguration()
    {
        Payplug::init(array(
            'secretKey' => 'cba',
            'apiVersion' => null
        ));

        $configuration = Payplug::getDefaultConfiguration();

        $this->assertEquals('cba', $configuration->getToken());
        $this->assertEquals('2019-06-14', $configuration->getApiVersion());
    }
    public function testCanInitializeConfiguration()
    {
        Payplug::init(array(
            'secretKey' => 'cba',
            'apiVersion' => '1970-01-01'
        ));

        $configuration = Payplug::getDefaultConfiguration();

        $this->assertEquals('cba', $configuration->getToken());
        $this->assertEquals('1970-01-01', $configuration->getApiVersion());
    }

    public function testDeprecatedCanInitializeConfiguration()
    {
        Payplug::setSecretKey('cba');

        $configuration = Payplug::getDefaultConfiguration();

        $this->assertEquals('cba', $configuration->getToken());
    }

    public function testCannotInitializeConfigurationWhenLiveTokenIsNotAString()
    {
        $this->setExpectedException('\PayPlug\Exception\ConfigurationException');
        Payplug::init(array(
            'secretKey' => true,
            'apiVersion' => '1970-01-01',
        ));
    }

    public function testDeprecatedCannotInitializeConfigurationWhenLiveTokenIsNotAString()
    {
        $this->setExpectedException('\PayPlug\Exception\ConfigurationException');
        Payplug::setSecretKey(true);
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

    public function testDeprecatedCannotInitializeConfigurationWhenTestTokenIsArray()
    {
        $this->setExpectedException('\PayPlug\Exception\ConfigurationException');

        Payplug::setSecretKey(array(
            'LIVE_TOKEN' => 'cba'
        ));
    }

    public function testCanGetAToken()
    {
        $configuration = Payplug::init(array('secretKey' => 'cba', 'apiVersion' => null));
        $this->assertEquals('cba', $configuration->getToken());
    }

    public function testDeprecatedCanGetAToken()
    {
        $configuration = Payplug::setSecretKey('cba');
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

    public function testDeprecatedCanSetDefaultConfiguration()
    {
        $configuration = Payplug::setSecretKey('abc');
        Payplug::setDefaultConfiguration($configuration);
        $this->assertEquals($configuration, Payplug::getDefaultConfiguration());
    }
}
