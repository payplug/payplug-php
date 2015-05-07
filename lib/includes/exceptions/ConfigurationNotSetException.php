<?php

/**
 * Thrown when there was a problem with a ClientConfiguration.
 */
class PayPlug_ConfigurationException extends PayPlug_PayPlugException
{
}

/**
 * Unable to find a client configuration exception.
 */
class PayPlug_ConfigurationNotSetException extends PayPlug_ConfigurationException
{
}