<?php

/**
 * The client configuration
 */
class PayPlug_ClientConfiguration
{
    /**
     * @var PayPlug_ClientConfiguration|null The default configuration that should be used when no configuration is
     * provided.
     */
    private static $_defaultConfiguration = null;

    /**
     * @var string The live token.
     */
    private $_liveToken;
    /**
     * @var string The test token.
     */
    private $_testToken;
    /**
     * @var bool True for Test mode. False for Live mode.
     */
    private $_isTestMode;

    /**
     * Constructor for a configuration.
     *
     * @param   string  $liveToken  The live token
     * @param   string  $testToken  The test token
     * @param   bool    $isTestMode Test mode is enabled
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
     * <pre>
     * Expected array format for argument $configuration :
     * $configuration['LIVE_TOKEN'] = 'YOUR LIVE TOKEN'
     *               ['TEST_TOKEN'] = 'YOUR TEST TOKEN'
     *               ['TEST_MODE_ENABLE'] = true for test mode, false for live mode
     * </pre>
     *
     * @param   array   $configuration  the configuration parameters
     *
     * @return PayPlug_ClientConfiguration  the new client configuration
     *
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
     * Gets the token corresponding to the mode currently in use (Live token or Test token).
     *
     * @return  string  The current token
     */
    public function getToken()
    {
        return $this->_isTestMode ? $this->_testToken : $this->_liveToken;
    }

    /**
     * Gets the mode currently in use.
     *
     * @return  boolean True if test mode is enabled. False if live mode is enabled.
     */
    public function isTestMode()
    {
        return $this->_isTestMode;
    }

    /**
     * Sets the mode to use. (Live mode or Test mode)
     *
     * @param   boolean $isTestMode true for Test mode, false for Live mode
     */
    public function setTestMode($isTestMode)
    {
        $this->_isTestMode = $isTestMode;
    }


    /**
     * Gets the default global configuration.
     *
     * @return  PayPlug_ClientConfiguration The last client configuration
     *
     * @throws  PayPlug_ConfigurationNotSetException    when the global configuration was not set.
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
     * passed to methods.
     *
     * @param   PayPlug_ClientConfiguration $defaultConfiguration   the new default configuration
     */
    public static function setDefaultConfiguration($defaultConfiguration)
    {
        self::$_defaultConfiguration = $defaultConfiguration;
    }
}