<?php

/**
 * @group functional
 * @group ci
 * @group recommended
 */
class HttpClientFunctionalTest extends PHPUnit_Framework_TestCase
{
    protected $_configuration;

    protected function setUp()
    {
        $this->_configuration = new Payplug_ClientConfiguration('abc', 'cba', true);
    }

    public function testAPIRequest()
    {
        $this->setExpectedException('PayPlug_UnauthorizedException');
        $httpClient = new PayPlug_HttpClient($this->_configuration);
        $httpClient->get(PayPlug_APIRoutes::API_BASE_URL . '/test');
    }
}
