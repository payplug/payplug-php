<?php

/**
 * Base class for all the exceptions that are related to Payplug.
 */
class PayplugException extends Exception
{
}

/**
 * This exception is thrown whenever you attempt to load your parameters
 * from Payplug but you have not activate your account. You must only use in
 * test mode.
 */
class ForbiddenCredentialsException extends PayplugException
{
}

/**
 * This exception is thrown whenever you attempt to load your parameters
 * from Payplug but have provided a wrong email and/or password.
 */
class InvalidCredentialsException extends PayplugException
{
}

/**
 * This exception is thrown whenever an IPN can't be validated. It means that it
 * could have been intercepted by someone between Payplug and you, so you should
 * probably not trust it.
 */
class InvalidSignatureException extends PayplugException
{
}

/**
 * This exception is thrown whenever you try to set the `ipnUrl` or `returnUrl`
 * field with a value that doesn't start with `http://` or `https://`.
 */
class MalformedURLException extends PayplugException
{
}

/**
 * This exception is thrown whenever there is an error while connecting
 * to Payplug.
 */
class NetworkException extends PayplugException
{
}

/**
 * This exception is thrown whenever you are trying to perform an operation
 * that requires the merchant's parameters, which have not been provided.
 */
class ParametersNotSetException extends PayplugException
{
}

/**
 * This exception is thrown whenever you are trying to generate a payment url
 * without providing a required parameter.
 */
class MissingRequiredParameterException extends PayplugException
{
}

