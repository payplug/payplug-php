<?php
namespace Payplug\Exception;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class HttpExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $exception = new HttpException('message_content', 'http_response_content', 808);
        $this->assertContains('Payplug\Exception\HttpException', (string)$exception);
        $this->assertContains('message_content', (string)$exception);
        $this->assertContains('http_response_content', (string)$exception);
        $this->assertContains('808', (string)$exception);
    }

    public function testGetHttpResponse()
    {
        $exception = new HttpException('message_content', 'http_response_content', 808);
        $this->assertEquals('http_response_content', $exception->getHttpResponse());
    }

    public function testGetErrorObjectWhenErrorIsJson()
    {
        $exception = new HttpException('message_content', '{"error": "an_error"}', 808);
        $this->assertEquals(array('error' => 'an_error'), $exception->getErrorObject());
    }

    public function testGetErrorObjectWhenErrorIsNotJson()
    {
        $exception = new HttpException('message_content', '{}not_json{}', 808);
        $this->assertNull($exception->getErrorObject());
    }
}