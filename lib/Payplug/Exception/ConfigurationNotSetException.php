<?php
namespace Payplug\Exception;

/**
 * Thrown when there was a problem with a Authentication.
 */
class ConfigurationException extends \Payplug\Exception\PayPlugException
{
}

/**
 * Unable to find a client configuration exception.
 */
class ConfigurationNotSetException extends \Payplug\Exception\ConfigurationException
{
}
