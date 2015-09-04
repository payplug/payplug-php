<?php
namespace Payplug\Exception;

/**
 * Dependency not satisfied exception.
 */
class DependencyException extends \Payplug\Exception\PayPlugException
{
}

/**
 * Wrong PHP version exception.
 */
class PHPVersionException extends DependencyException
{
}
