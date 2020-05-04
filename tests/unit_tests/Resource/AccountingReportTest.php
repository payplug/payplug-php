<?php
namespace Payplug\Resource;
use Payplug;
use Payplug\Core\HttpClient;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class AccountingReportTest extends \PHPUnit_Framework_TestCase
{
    private $_requestMock;
    private $_configuration;

    protected function setUp()
    {
        $this->_configuration = new Payplug\Payplug('abc');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        Payplug\Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    protected function setUpTwice()
    {
        $this->_configuration = new Payplug\Payplug('abc','1970-01-01');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        Payplug\Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    public function testCreateAccountingReportFromAttributes()
    {
        $report= AccountingReport::fromAttributes(array(
            'start_date' => '2020-01-01',
            'object' => 'accounting_report',
            'notification_url' => 'notification_url',
            'end_date' => '2020-04-30',
            'id' => 'ar_1GKEACvltTVXT5muBd3AQv',
            'file_available_until' => 1588083743,
            'temporary_url' => 'temporary_url'
        ));

        $this->assertEquals('ar_1GKEACvltTVXT5muBd3AQv', $report->id);
        $this->assertEquals('2020-01-01', $report->start_date);
        $this->assertEquals('2020-04-30', $report->end_date);
        $this->assertEquals('notification_url', $report->notification_url);
        $this->assertEquals('temporary_url', $report->temporary_url);
        $this->assertEquals(1588083743, $report->file_available_until);
    }

    public function testAccountingReportCreate()
    {
        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));

        $this->_requestMock
            ->expects($this->atLeastOnce())
            ->method('setopt')
            ->will($this->returnCallback(function($option, $value = null) {
                switch($option) {
                    case CURLOPT_POSTFIELDS:
                        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = json_decode($value, true);
                        return true;
                }
                return true;
            }));
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

        $report = AccountingReport::create(array(
            'start_date' => '2020-01-01',
            'object' => 'accounting_report',
            'notification_url' => 'notification_url',
            'end_date' => '2020-04-30',
            'id' => 'ar_1GKEACvltTVXT5muBd3AQv',
            'file_available_until' => 1588083743,
            'temporary_url' => 'temporary_url'
        ));

        $this->assertTrue(is_array($GLOBALS['CURLOPT_POSTFIELDS_DATA']));
        $this->assertEquals('ok', $report->status);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testAccountingReportRetrieve()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

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
                    case CURLOPT_URL:
                        $GLOBALS['CURLOPT_URL_DATA'] = $value;
                        return true;
                }
                return true;
            }));

        $report = AccountingReport::retrieve('a_report_id');

        $this->assertStringEndsWith('a_report_id', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertEquals('ok', $report->status);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRetrieveConsistentAccountingReportWhenIdIsUndefined()
    {
        $this->setExpectedException('\PayPlug\Exception\UndefinedAttributeException');

        $report= AccountingReport::fromAttributes(array('this_report' => 'has_no_id'));
        $report->getConsistentResource();
    }

    public function testRetrieveConsistentAccountingReport()
    {
        function testRetrieveConsistentAccountingReport_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"id": "ar_345"}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnValue(true));
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

        $report1 = AccountingReport::fromAttributes(array('id' => 'ar_123'));
        $report2 = $report1->getConsistentResource($this->_configuration);

        $this->assertEquals('ar_123', $report1->id);
        $this->assertEquals('ar_345', $report2->id);
    }
}
