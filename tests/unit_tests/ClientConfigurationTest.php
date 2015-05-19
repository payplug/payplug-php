<?php

/**
 * @group unit
 * @group ci
 */
class ClientConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testCanInitializeConfiguration()
    {
        PayPlug_ClientConfiguration::initialize(array(
                'LIVE_TOKEN'        => 'abc',
                'TEST_TOKEN'        => 'cba',
                'TEST_MODE_ENABLED' => true
            )
        );

        $configuration = PayPlug_ClientConfiguration::getDefaultConfiguration();

        $this->assertEquals('cba', $configuration->getToken());
    }

    public function testCannotInitializeConfigurationWhenLiveTokenIsNotSet()
    {
        $this->setExpectedException('PayPlug_ConfigurationException');

        PayPlug_ClientConfiguration::initialize(array(
                'TEST_TOKEN'        => 'cba',
                'TEST_MODE_ENABLED' => true
            )
        );
    }

    public function testCannotInitializeConfigurationWhenLiveTokenIsNotAString()
    {
        $this->setExpectedException('PayPlug_ConfigurationException');

        PayPlug_ClientConfiguration::initialize(array(
                'LIVE_TOKEN'        => true,
                'TEST_TOKEN'        => 'cba',
                'TEST_MODE_ENABLED' => true
            )
        );
    }

    public function testCannotInitializeConfigurationWhenTestTokenIsNotSet()
    {
        $this->setExpectedException('PayPlug_ConfigurationException');

        PayPlug_ClientConfiguration::initialize(array(
                'LIVE_TOKEN'        => 'cba',
                'TEST_MODE_ENABLED' => true
            )
        );
    }

    public function testCannotInitializeConfigurationWhenTestTokenIsNotAString()
    {
        $this->setExpectedException('PayPlug_ConfigurationException');

        PayPlug_ClientConfiguration::initialize(array(
                'LIVE_TOKEN'        => 'cba',
                'TEST_TOKEN'        => true,
                'TEST_MODE_ENABLED' => true
            )
        );
    }

    public function testCannotInitializeConfigurationWhenModeIsNotSet()
    {
        $this->setExpectedException('PayPlug_ConfigurationException');

        PayPlug_ClientConfiguration::initialize(array(
                'LIVE_TOKEN'        => 'cba',
                'TEST_TOKEN'        => 'cba'
            )
        );
    }

    public function testCannotInitializeConfigurationWhenModeIsNotBoolean()
    {
        $this->setExpectedException('PayPlug_ConfigurationException');

        PayPlug_ClientConfiguration::initialize(array(
                'LIVE_TOKEN'        => 'cba',
                'TEST_TOKEN'        => 'cba',
                'TEST_MODE_ENABLED' => 'dumb'
            )
        );
    }

    public function testCanSwitchMode()
    {
        $configuration = new PayPlug_ClientConfiguration('abc', 'cba', true);
        $this->assertTrue($configuration->isTestMode());
        $configuration->setTestMode(false);
        $this->assertFalse($configuration->isTestMode());
        $configuration->setTestMode(true);
        $this->assertTrue($configuration->isTestMode());
    }

    public function testCanGetATestToken()
    {
        $configuration = new PayPlug_ClientConfiguration('abc', 'cba', true);
        $this->assertEquals('cba', $configuration->getToken());
    }

    public function testCanGetALiveToken()
    {
        $configuration = new PayPlug_ClientConfiguration('abc', 'cba', false);
        $this->assertEquals('abc', $configuration->getToken());
    }

    /**
     * @runInSeparateProcess so that static default configuration is cleared before the test
     */
    public function testThrowsExceptionWhenDefaultConfigurationIsNotSet()
    {
        $this->setExpectedException('PayPlug_ConfigurationNotSetException');
        PayPlug_ClientConfiguration::getDefaultConfiguration();
    }

    public function testCanSetDefaultConfiguration()
    {
        $configuration = new PayPlug_ClientConfiguration('abc', 'cba', false);
        PayPlug_ClientConfiguration::setDefaultConfiguration($configuration);
        $this->assertEquals($configuration, PayPlug_ClientConfiguration::getDefaultConfiguration());
    }
}