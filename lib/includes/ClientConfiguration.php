<?php

/**
 * The client configuration
 */
class PayPlug_ClientConfiguration
{
    private static $_defaultConfiguration = null;
    private $_liveToken;
    private $_testToken;
    private $_isTestMode;

    /**
     * @param string $liveToken The live token
     * @param string $testToken The test token
     * @param bool $isTestMode Test mode is enabled
     */
    public function __construct($liveToken, $testToken, $isTestMode = true)
    {
        $this->_liveToken = $liveToken;
        $this->_testToken = $testToken;
        $this->_isTestMode = $isTestMode;
    }

    /**
     * Initializes a PayPlug_ClientConfiguration and sets it as the new default global configuration.
     * It also performs some checks before saving the configuration.
     *
     * Expected array format for argument $configuration :
     * $configuration['LIVE_TOKEN'] = 'YOUR LIVE TOKEN'
     *               ['TEST_TOKEN'] = 'YOUR TEST TOKEN'
     *               ['TEST_MODE_ENABLE'] = true for test mode, false for live mode
     *
     * @param array $configuration the configuration parameters
     * @return PayPlug_ClientConfiguration the new client configuration
     * @throws PayPlug_ConfigurationException
     */
    public static function initialize(array $configuration)
    {
        if (!array_key_exists('LIVE_TOKEN', $configuration)) {
            throw new PayPlug_ConfigurationException('Missing key "LIVE_TOKEN" in configuration.');
        }
        if (!array_key_exists('TEST_TOKEN', $configuration)) {
            throw new PayPlug_ConfigurationException('Missing key "TEST_TOKEN" in configuration.');
        }
        if (!array_key_exists('TEST_MODE_ENABLED', $configuration)) {
            throw new PayPlug_ConfigurationException('Missing key "TEST_MODE_ENABLED" in configuration.');
        }
        if (!is_bool($configuration['TEST_MODE_ENABLED'])) {
            throw new PayPlug_ConfigurationException('Expected a boolean value for key "TEST_MODE_ENABLES".');
        }
        if (!is_string($configuration['LIVE_TOKEN']) || !is_string($configuration['TEST_TOKEN'])) {
            throw new PayPlug_ConfigurationException('Expected string values for keys "LIVE_TOKEN" and "TEST_TOKEN".');
        }

        $clientConfiguration = new PayPlug_ClientConfiguration(
            $configuration['LIVE_TOKEN'],
            $configuration['TEST_TOKEN'],
            $configuration['TEST_MODE_ENABLED']
        );

        self::setDefaultConfiguration($clientConfiguration);

        return $clientConfiguration;
    }

    /**
     * @return string The current token
     */
    public function getToken()
    {
        return $this->_isTestMode ? $this->_testToken : $this->_liveToken;
    }

    /**
     * @return boolean True if test mode is enabled
     */
    public function isTestMode()
    {
        return $this->_isTestMode;
    }

    /**
     * Sets the mode (live or test)
     * @param boolean $isTestMode true if test mode, false if live mode
     */
    public function setTestMode($isTestMode)
    {
        $this->_isTestMode = $isTestMode;
    }

    /**
     * @return string An absolute path to the API SSL certificate.
     */
    public function getAPISSLCertificatePath()
    {
        return dirname(__DIR__) . '/certs/PayPlug.ca';
    }

    /**
     * @return PayPlug_ClientConfiguration The last client configuration
     * @throws PayPlug_ConfigurationNotSetException
     */
    public static function getDefaultConfiguration()
    {
        if (self::$_defaultConfiguration === null) {
                throw new PayPlug_ConfigurationNotSetException('Unable to find a configuration.');
        }

        return self::$_defaultConfiguration;
    }

    /**
     * Sets the new default client configuration. This configuration will be used when no configuration is explicitly
     * used in API Resources objects parameters.
     * @param PayPlug_ClientConfiguration $defaultConfiguration the new default configuration
     */
    public static function setDefaultConfiguration($defaultConfiguration)
    {
        self::$_defaultConfiguration = $defaultConfiguration;
    }
}