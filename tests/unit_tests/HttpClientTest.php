<?php

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class HttpClientTest extends PHPUnit_Framework_TestCase
{
    private $_httpClient;
    private $_requestMock;

    protected function setUp()
    {
        $this->_httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));

        $this->_requestMock = $this->getMock('PayPlug_IHttpRequest');
        PayPlug_HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    public function testPost()
    {
        function testPost_getinfo($option) {
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
            ->will($this->returnCallback('testPost_getinfo'));

        $result = $this->_httpClient->post('somewhere');

        $this->assertEquals(array('status' => 'ok'), $result['httpResponse']);
        $this->assertEquals(200, $result['httpStatus']);
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
            ->will($this->returnCallback('testPost_getinfo'));

        $result = $this->_httpClient->get('somewhere_else');

        $this->assertEquals(array('status' => 'ok'), $result['httpResponse']);
        $this->assertEquals(200, $result['httpStatus']);
    }

    public function testError500()
    {
        function testError500_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 500;
            }
            return null;
        }

        $this->setExpectedException('PayPlug_PayPlugServerException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testError500_getinfo'));

        $this->_httpClient->get('somewhere');
    }

    public function testError400()
    {
        function testError400_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 400;
            }
            return null;
        }

        $this->setExpectedException('PayPlug_BadRequestException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testError400_getinfo'));

        $this->_httpClient->get('somewhere');
    }

    public function testError401()
    {
        function testError401_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 401;
            }
            return null;
        }

        $this->setExpectedException('PayPlug_UnauthorizedException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testError401_getinfo'));

        $this->_httpClient->get('somewhere');
    }

    public function testError403()
    {
        function testError403_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 403;
            }
            return null;
        }

        $this->setExpectedException('PayPlug_ForbiddenException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testError403_getinfo'));

        $this->_httpClient->get('somewhere');
    }

    public function testError404()
    {
        function testError404_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 404;
            }
            return null;
        }

        $this->setExpectedException('PayPlug_NotFoundException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testError404_getinfo'));

        $this->_httpClient->get('somewhere');
    }

    public function testError405()
    {
        function testError405_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 405;
            }
            return null;
        }

        $this->setExpectedException('PayPlug_NotAllowedException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testError405_getinfo'));

        $this->_httpClient->get('somewhere');
    }

    public function testErrorUnknown()
    {
        function testError418_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 418;
            }
            return null;
        }

        $this->setExpectedException('PayPlug_HttpException');

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testError418_getinfo'));

        $this->_httpClient->get('somewhere');
    }

    public function testNotEmptyData()
    {
        function testNotEmptyData_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        // Anonymous functions not available with PHP 5.2 :(
        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = null;
        function testNotEmptyData_setopt($option, $value = null) {
            switch($option) {
                case CURLOPT_POSTFIELDS:
                    $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = json_decode($value, true);
                    return true;
            }

            return true;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testNotEmptyData_getinfo'));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback('testNotEmptyData_setopt'));

        $result = $this->_httpClient->get('somewhere_else', array('foo' => 'bar'));

        $this->assertEquals(array('foo' => 'bar'), $GLOBALS['CURLOPT_POSTFIELDS_DATA']);
        $this->assertEquals(array('status' => 'ok'), $result['httpResponse']);
        $this->assertEquals(200, $result['httpStatus']);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    function testInvalidAPIResponse()
    {
        $this->setExpectedException('PayPlug_UnexpectedAPIResponseException');

        function testInvalidAPIResponse_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('This is not JSON'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testInvalidAPIResponse_getinfo'));

        $this->_httpClient->get('somewhere_else');
    }
}