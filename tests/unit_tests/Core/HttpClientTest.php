<?php
namespace Payplug;
use \Payplug\Core\HttpClient;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    private $_httpClient;
    private $_requestMock;

    protected function setUp()
    {
        $this->_httpClient = new HttpClient(new Payplug('abc'));

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    protected function setUpTwice()
    {
        $this->_httpClient = new HttpClient(new Payplug('abc','1970-01-01'));

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    public function testPost()
    {

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $result = $this->_httpClient->post('somewhere');

        $this->assertEquals(array('status' => 'ok'), $result['httpResponse']);
        $this->assertEquals(200, $result['httpStatus']);
    }

    public function testPatch()
    {
        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $result = $this->_httpClient->patch('somewhere');

        $this->assertEquals(array('status' => 'ok'), $result['httpResponse']);
        $this->assertEquals(200, $result['httpStatus']);
    }

    public function testDelete()
    {
        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 204;
                }
                return null;
            }));

        $result = $this->_httpClient->delete('somewhere');

        $this->assertEquals(array('status' => 'ok'), $result['httpResponse']);
        $this->assertEquals(204, $result['httpStatus']);
    }

    public function testGet()
    {
        function testGet_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $result = $this->_httpClient->get('somewhere_else');

        $this->assertEquals(array('status' => 'ok'), $result['httpResponse']);
        $this->assertEquals(200, $result['httpStatus']);
    }

    public function testError500()
    {

        $this->setExpectedException('\PayPlug\Exception\PayPlugServerException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 500;
                }
                return null;
            }));

        $this->_httpClient->get('somewhere');
    }

    public function testError400()
    {

        $this->setExpectedException('\PayPlug\Exception\BadRequestException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 400;
                }
                return null;
            }));

        $this->_httpClient->get('somewhere');
    }

    public function testError401()
    {

        $this->setExpectedException('\PayPlug\Exception\UnauthorizedException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 401;
                }
                return null;
            }));

        $this->_httpClient->get('somewhere');
    }

    public function testError403()
    {

        $this->setExpectedException('\PayPlug\Exception\ForbiddenException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 403;
                }
                return null;
            }));

        $this->_httpClient->get('somewhere');
    }

    public function testError404()
    {

        $this->setExpectedException('\PayPlug\Exception\NotFoundException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 404;
                }
                return null;
            }));

        $this->_httpClient->get('somewhere');
    }

    public function testError405()
    {

        $this->setExpectedException('\PayPlug\Exception\NotAllowedException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 405;
                }
                return null;
            }));

        $this->_httpClient->get('somewhere');
    }

    public function testErrorUnknown()
    {

        $this->setExpectedException('\PayPlug\Exception\HttpException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 418;
                }
                return null;
            }));

        $this->_httpClient->get('somewhere');
    }

    public function testNotEmptyData()
    {
        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback(function($option, $value = null) {
                switch($option) {
                    case CURLOPT_POSTFIELDS:
                        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = json_decode($value, true);
                        return true;
                }
                return true;
            }));

        $result = $this->_httpClient->get('somewhere_else', array('foo' => 'bar'));

        $this->assertEquals(array('foo' => 'bar'), $GLOBALS['CURLOPT_POSTFIELDS_DATA']);
        $this->assertEquals(array('status' => 'ok'), $result['httpResponse']);
        $this->assertEquals(200, $result['httpStatus']);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    function testInvalidAPIResponse()
    {
        $this->setExpectedException('\PayPlug\Exception\UnexpectedAPIResponseException');



        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('This is not JSON'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $this->_httpClient->get('somewhere_else');
    }

    function testConnectionError()
    {
        $this->setExpectedException('\PayPlug\Exception\ConnectionException');



        function testConnectionError_errno($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 0;
            }
            return null;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(false));
        $this->_requestMock
            ->expects($this->any())
            ->method('errno')
            ->will($this->returnValue(7)); // CURLE_COULDNT_CONNECT
        $this->_requestMock
            ->expects($this->any())
            ->method('error')
            ->will($this->returnValue('Failed to connect() to host or proxy.'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 0;
                }
                return null;
            }));

        $this->_httpClient->get('somewhere');
    }

    function testFormatUserAgentProduct()
    {
        $result = \Payplug\Test\TestUtils::invokePrivateMethod(
            $this->_httpClient, 'formatUserAgentProduct',
            array('PayPlug-PHP', '2.2.1' , 'PHP/5.5.34; curl/7.43.0')
        );

        $this->assertEquals($result, 'PayPlug-PHP/2.2.1 (PHP/5.5.34; curl/7.43.0)');
    }

    function testGetUserAgent()
    {
        $userAgent = $this->_httpClient->getUserAgent();
        // Expected result is something like 'PayPlug-PHP/1.0.0 (PHP/5.5.35; curl/7.44.0)'
        $this->assertRegExp(
            '/^PayPlug-PHP\/(\d+\.?){1,3} \(PHP\/(\d+\.?){1,3}(\w|\+|\.|\-)*; curl\/(\d+\.?){1,3}\)$/',
            $userAgent
        );
    }

    function testGetUserAgentWithAdditionalProduct()
    {
        \Payplug\Core\HttpClient::addDefaultUserAgentProduct('PayPlug-Test', '1.0.0', 'Comment/1.6.3');
        \Payplug\Core\HttpClient::addDefaultUserAgentProduct('Another-Test', '5.8.13');
        $userAgent = $this->_httpClient->getUserAgent();
        $this->assertStringEndsWith(' PayPlug-Test/1.0.0 (Comment/1.6.3) Another-Test/5.8.13', $userAgent);
    }
}
