<?php

/**
 * PayPlug's generic exception.
 */
abstract class PayPlug_PayPlugException extends Exception
{
    /**
     * @return string A string representation of the exception, containing the exception class name, the error code \
     *                and the message of the exception.
     */
    public function __toString()
    {
        return get_class($this) . ": [{$this->code}]: {$this->message}\n";
    }
}