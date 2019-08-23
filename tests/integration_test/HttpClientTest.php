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
        $this->_configuration = new Payplug('abc','1970-01-01');
    }

    public function testAPIRequest()
    {
        $httpClient = new Core\HttpClient($this->_configuration);
        $response = $httpClient->testRemote();
        $this->assertEquals(200, $response['httpStatus']);
    }
}
