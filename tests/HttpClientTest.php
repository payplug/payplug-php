<?php

require_once 'lib/PayPlug.php';

class HttpClientTest extends PHPUnit_Framework_TestCase {
    protected $_configuration;

    protected function setUp()
    {
        $this->_configuration = new Payplug_ClientConfiguration(TestsConfig::LIVE_TOKEN, TestsConfig::TEST_TOKEN, false);
    }

    public function testCanRequestAPI()
    {
        $httpClient = new PayPlug_HttpClient($this->_configuration);
        $response = $httpClient->get(PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::CREATE_PAYMENT));
        $this->assertEquals($response['httpStatus'], 200);
    }
}
