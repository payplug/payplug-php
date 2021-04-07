<?php
namespace Payplug;

/**
 * @group functional
 * @group ci
 * @group recommended
 */
class HttpClientFunctionalTest extends \PHPUnit\Framework\TestCase
{
    protected $_configuration;

    /**
     * @before
     */
    protected function setUpTest()
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
