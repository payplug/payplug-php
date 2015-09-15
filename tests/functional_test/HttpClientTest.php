<?php
namespace Payplug;

/**
 * @group functional
 * @group ci
 * @group recommended
 */
class HttpClientFunctionalTest extends \PHPUnit_Framework_TestCase
{
    protected $_configuration;

    protected function setUp()
    {
        $this->_configuration = new \Payplug\Payplug('abc');
    }

    public function testAPIRequest()
    {
        $httpClient = new \Payplug\Core\HttpClient($this->_configuration);
        $response = $httpClient->testRemote();
        $this->assertEquals(200, $response['httpStatus']);
    }
}
