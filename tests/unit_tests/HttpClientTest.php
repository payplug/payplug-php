<?php

/**
 * @group unit
 */
class HttpClientTest extends PHPUnit_Framework_TestCase
{
    private $_requestMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('PayPlug_IHttpRequest');
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

        $httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));
        $result = $httpClient->post('somewhere', null, $this->_requestMock);

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

        $httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));
        $result = $httpClient->get('somewhere_else', null, $this->_requestMock);

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

        $httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));
        $httpClient->get('somewhere', null, $this->_requestMock);
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

        $httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));
        $httpClient->get('somewhere', null, $this->_requestMock);
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

        $httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));
        $httpClient->get('somewhere', null, $this->_requestMock);
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

        $httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));
        $httpClient->get('somewhere', null, $this->_requestMock);
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

        $httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));
        $httpClient->get('somewhere', null, $this->_requestMock);
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

        $httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));
        $httpClient->get('somewhere', null, $this->_requestMock);
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

        $httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));
        $httpClient->get('somewhere', null, $this->_requestMock);
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
        
        function testNotEmptyData_setopt($option, $value = null) {
            switch($option) {
                case CURLOPT_POSTFIELDS:
                    $this->assertEquals(array("'foo': 'bar'"), json_decode($value));
                    return true;
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
            ->will($this->returnCallback('testNotEmptyData_getinfo'));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback('testNotEmptyData_setopt'));

        $httpClient = new PayPlug_HttpClient(new PayPlug_ClientConfiguration('abc', 'cba', 123));
        $result = $httpClient->get('somewhere_else', array('foo' => 'bar'), $this->_requestMock);

        $this->assertEquals(array('status' => 'ok'), $result['httpResponse']);
        $this->assertEquals(200, $result['httpStatus']);
    }
}