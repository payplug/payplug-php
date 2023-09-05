<?php


namespace Payplug;
use Payplug;
use \Payplug\Core\HttpClient;
use Payplug\Core\APIRoutes;
use PHPUnit\Framework\TestCase;

class PluginTelemetryTest extends TestCase
{

    private $_requestMock;
    private $_httpClient;

    /**
     * @before
     */
    protected function setUpTest()
    {
        $this->_configuration = new Payplug\Payplug('abc');
        $this->_httpClient = new HttpClient(new \Payplug\Payplug('abc'));
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->createMock('Payplug\Core\IHttpRequest');
        Payplug\Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    /**
     * Verify the behavior of send() method in the PluginTelemetry class
     * with a mocked API URL that raises an exception
     *
     * @throws Exception\ConnectionException
     * @throws Exception\HttpException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public function testSendWithBadMockedURL()
    {
        $this->expectException('Payplug\Exception\HttpException');
        // Set a mocked MPDC API URL that will raise an exception
        APIRoutes::setMerchantPluginsDataCollectorService('bad_mocked_mpdc_api_url');


        // Mock the post method of the HTTP client
        $this->_requestMock->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"not ok"}'));

        $result = $this->_httpClient->post(APIRoutes::$MERCHANT_PLUGINS_DATA_COLLECTOR_RESOURCE);


        // Data to send to the MPDC microservice
        $data = array(
            'version' => '4.0.0',
            'php_version' => '8.2.1',
            'name' => 'value',
            'from' => 'save',
            'domains' => array(
                array(
                    'url' => 'www.mywebsite.com',
                    'default' => true
                )
            ),
            'configurations' => array(
                'name' => 'value'
            ),
            'modules' => array(
                array(
                    'name' => 'value',
                    'version' => 'value'
                )
            )
        );
        $data = json_encode($data);

        // call send and assert
        PluginTelemetry::send($data);

    }
}