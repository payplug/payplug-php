<?php

/**
 * @group functional
 * @group ci
 */
class HttpClientFunctionalTest extends PHPUnit_Framework_TestCase
{
    protected $_configuration;

    protected function setUp()
    {
        $this->_configuration = new Payplug_ClientConfiguration('', '', true);
    }

    public function testAPIRequest()
    {
        //TODO
    }
}
