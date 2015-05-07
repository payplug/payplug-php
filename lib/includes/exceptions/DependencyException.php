<?php

/**
 * Dependency not satisfied exception.
 */
class PayPlug_DependencyException extends PayPlug_PayPlugException
{
}

/**
 * Wrong PHP version exception.
 */
class PayPlug_PHPVersionException extends PayPlug_DependencyException
{
}
