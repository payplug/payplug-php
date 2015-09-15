<?php
namespace PayplugTest;
use \Payplug\Core\APIRoutes as APIRoutes;
/**
 * @group unit
 * @group ci
 * @group recommended
 */
class APIRoutesTest extends \PHPUnit_Framework_TestCase
{
    public function testThatRouteStartsWithBaseURL()
    {
        $route = APIRoutes::getRoute(APIRoutes::CREATE_PAYMENT);
        $expected = APIRoutes::$API_BASE_URL . '/v' . APIRoutes::API_VERSION . '/';
        $beginRoute = substr($route, 0, strlen($expected));
        $this->assertEquals($expected, $beginRoute);
    }

    public function testCreatePaymentRoute()
    {
        $route = APIRoutes::getRoute(APIRoutes::CREATE_PAYMENT);
        $expected = '/payments';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testRetrievePaymentRoute()
    {
        $route = APIRoutes::getRoute(APIRoutes::RETRIEVE_PAYMENT, array('PAYMENT_ID' => 'foo'));
        $expected = '/payments/foo';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testListpaymentsRoute()
    {
        $route = APIRoutes::getRoute(APIRoutes::LIST_PAYMENTS);
        $expected = '/payments';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testListpaymentspaginationRoute()
    {
        $pagination = array('perPage' => 5, 'page' => 1);
        $route = APIRoutes::getRoute(APIRoutes::LIST_PAYMENTS, array(), $pagination);
        $expected = '/payments?perPage=5&page=1';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testCreateRefundRoute()
    {
        $route = APIRoutes::getRoute(APIRoutes::CREATE_REFUND, array('PAYMENT_ID' => 'foo'));
        $expected = '/payments/foo/refunds';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testRetrieveRefundRoute()
    {
        $route = APIRoutes::getRoute(APIRoutes::RETRIEVE_REFUND, array('PAYMENT_ID' => 'foo', 'REFUND_ID' => 'bar'));
        $expected = '/payments/foo/refunds/bar';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testListRefundsRoute()
    {
        $route = APIRoutes::getRoute(APIRoutes::LIST_REFUNDS, array('PAYMENT_ID' => 'foo'));
        $expected = '/payments/foo/refunds';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }
}
