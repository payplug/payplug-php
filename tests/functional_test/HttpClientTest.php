<?php

require_once 'lib/PayPlug.php';

/**
 * @group functional
 */
class HttpClientFunctionalTest extends PHPUnit_Framework_TestCase
{
    protected $_configuration;

    protected function setUp()
    {
        $this->_configuration = new Payplug_ClientConfiguration(TestsConfig::LIVE_TOKEN, TestsConfig::TEST_TOKEN, true);
    }

    public function testCanRequestAPI()
    {
        $httpClient = new PayPlug_HttpClient($this->_configuration);
        $response = $httpClient->get(PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::CREATE_PAYMENT));
        $this->assertEquals($response['httpStatus'], 200);
    }
}
