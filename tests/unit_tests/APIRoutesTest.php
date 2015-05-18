<?php

/**
 * @group unit
 */
class APIRoutesTest extends PHPUnit_Framework_TestCase
{
    public function testThatRouteStartsWithBaseURL()
    {
        $route = PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::CREATE_PAYMENT);
        $expected = PayPlug_APIRoutes::API_BASE_URL . '/v' . PayPlug_APIRoutes::API_VERSION . '/';
        $beginRoute = substr($route, 0, strlen($expected));
        $this->assertEquals($expected, $beginRoute);
    }

    public function testCreatePaymentRoute()
    {
        $route = PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::CREATE_PAYMENT);
        $expected = '/payments';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testRetrievePaymentRoute()
    {
        $route = PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::RETRIEVE_PAYMENT, array('PAYMENT_ID' => 'foo'));
        $expected = '/payments/foo';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testCreateRefundRoute()
    {
        $route = PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::CREATE_REFUND, array('PAYMENT_ID' => 'foo'));
        $expected = '/payments/foo/refunds';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testRetrieveRefundRoute()
    {
        $route = PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::RETRIEVE_REFUND, array('PAYMENT_ID' => 'foo', 'REFUND_ID' => 'bar'));
        $expected = '/payments/foo/refunds/bar';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }

    public function testListRefundsRoute()
    {
        $route = PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::LIST_REFUNDS, array('PAYMENT_ID' => 'foo'));
        $expected = '/payments/foo/refunds';
        $endRoute = substr($route, -strlen($expected));
        $this->assertEquals($expected, $endRoute);
    }
}
