<?php

require_once(__DIR__ . "/../lib/Payplug.php");

class PayplugExceptionsTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException PayplugException
     */
    public function testPayplugException() {
        throw new PayplugException();
    }

    /**
     * @expectedException InvalidCredentialsException
     */
    public function testInvalidCredentialsException() {
        throw new InvalidCredentialsException();
    }

    /**
     * @expectedException InvalidSignatureException
     */
    public function testInvalidSignatureException() {
        throw new InvalidSignatureException();
    }

    /**
     * @expectedException NetworkException
     */
    public function testNetworkException() {
        throw new NetworkException();
    }

    /**
     * @expectedException NetworkException
     * @expectedExceptionCode 401
     * @expectedExceptionMessage HTTP error (401) : Unauthorized
     */
    public function testNetworkExceptionMessage() {
        throw new NetworkException("HTTP error (401) : Unauthorized", 401);
    }

    /**
     * @expectedException ParametersNotSetException
     */
    public function testParametersNotSetException() {
        throw new ParametersNotSetException();
    }
}

