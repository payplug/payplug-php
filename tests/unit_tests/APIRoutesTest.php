<?php
namespace PayplugTest;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class APIRoutesTest extends \PHPUnit_Framework_TestCase
{
    public function testThatRouteStartsWithBaseURL()
    {
        $route = \Payplug\APIRoutes::getRoute(\Payplug\APIRoutes::CREATE_PAYMENT);
        $expected = \Payplug\APIRoutes::$API_BASE_URL . '/v' . \Payplug\APIRoutes::API_VERSION . '/';
        $beginRoute = substr($route, 0, strlen($expected));
        $this->assertEquals($expected, $beginRoute);
    }

    public function testCreatePaymentRoute()
    {
        $route = \Payplug\APIRoutes::getRoute(\Payplug\APIRoutes::CREATE_PAYMENT);
        $expected = '/payments';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testRetrievePaymentRoute()
    {
        $route = \Payplug\APIRoutes::getRoute(\Payplug\APIRoutes::RETRIEVE_PAYMENT, array('PAYMENT_ID' => 'foo'));
        $expected = '/payments/foo';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testListpaymentsRoute()
    {
        $route = \Payplug\APIRoutes::getRoute(\Payplug\APIRoutes::LIST_PAYMENTS);
        $expected = '/payments';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testListpaymentspaginationRoute()
    {
        $pagination = array('perPage' => 5, 'page' => 1);
        $route = \Payplug\APIRoutes::getRoute(\Payplug\APIRoutes::LIST_PAYMENTS, array(), $pagination);
        $expected = '/payments?perPage=5&page=1';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testCreateRefundRoute()
    {
        $route = \Payplug\APIRoutes::getRoute(\Payplug\APIRoutes::CREATE_REFUND, array('PAYMENT_ID' => 'foo'));
        $expected = '/payments/foo/refunds';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testRetrieveRefundRoute()
    {
        $route = \Payplug\APIRoutes::getRoute(\Payplug\APIRoutes::RETRIEVE_REFUND, array('PAYMENT_ID' => 'foo', 'REFUND_ID' => 'bar'));
        $expected = '/payments/foo/refunds/bar';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testListRefundsRoute()
    {
        $route = \Payplug\APIRoutes::getRoute(\Payplug\APIRoutes::LIST_REFUNDS, array('PAYMENT_ID' => 'foo'));
        $expected = '/payments/foo/refunds';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }
}
