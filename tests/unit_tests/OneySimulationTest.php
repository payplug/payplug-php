<?php
namespace Payplug;
use Payplug;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class OneySimulationTest extends \PHPUnit_Framework_TestCase
{
    private $_requestMock;

    protected function setUp()
    {
        $this->_configuration = new Payplug\Payplug('abc');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    public function testGetOneyPaymentSimulation()
    {
        $data = array(
            'amount' => 50500,
            'country' => 'FR',
            'operations' => array(
                'x3_with_fees',
                'x4_with_fees',
            ),
        );

        $response = array(
            'x3_with_fees' => array(
                'effective_annual_percentage_rate' => 19.25,
                'nominal_annual_percentage_rate' => 17.74,
                'total_cost' => 732,
                'installments' => array(
                    array (
                        'date' => '2020-03-06T01:00:00.000Z',
                        'amount' => 16834,
                    ),
                    array (
                        'date' => '2020-04-06T00:00:00.000Z',
                        'amount' => 16833,
                    ),
                ),
                'down_payment_amount' => 17565,
            ),
            'x4_with_fees' => array(
                'effective_annual_percentage_rate' => 19.25,
                'nominal_annual_percentage_rate' => 17.74,
                'total_cost' => 732,
                'installments' => array(
                    array (
                        'date' => '2020-03-06T01:00:00.000Z',
                        'amount' => 16834,
                    ),
                    array (
                        'date' => '2020-04-06T00:00:00.000Z',
                        'amount' => 16833,
                    ),
                    array (
                        'date' => '2020-05-06T00:00:00.000Z',
                        'amount' => 16833,
                    ),
                ),
                'down_payment_amount' => 17565,
            )
        );

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode($response)));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 201;
                }
                return null;
            }));

        $simulations = OneySimulation::getSimulations($data);

        // check 3x with fees
        $this->assertArrayHasKey('x3_with_fees', $simulations);
        if (isset($simulations['x3_with_fees']) && $simulations['x3_with_fees']) {
            $x3_with_fees = $simulations['x3_with_fees'];

            $this->assertEquals(true, is_array($x3_with_fees));
            $this->assertEquals(19.25, $x3_with_fees['effective_annual_percentage_rate']);
            $this->assertEquals(17.74, $x3_with_fees['nominal_annual_percentage_rate']);
            $this->assertEquals(732, $x3_with_fees['total_cost']);

            $installments_x3 = $x3_with_fees['installments'];

            $this->assertEquals(2, count($installments_x3));
            $this->assertEquals('2020-03-06T01:00:00.000Z', $installments_x3[0]['date']);
            $this->assertEquals(16834, $installments_x3[0]['amount']);
            $this->assertEquals('2020-04-06T00:00:00.000Z', $installments_x3[1]['date']);
            $this->assertEquals(16833, $installments_x3[1]['amount']);

            $this->assertEquals(17565, $x3_with_fees['down_payment_amount']);
        }

        // check 4x with fees
        $this->assertArrayHasKey('x4_with_fees', $simulations);
        if (isset($simulations['x4_with_fees']) && $simulations['x4_with_fees']) {
            $x4_with_fees = $simulations['x4_with_fees'];

            $this->assertEquals(true, is_array($x4_with_fees));
            $this->assertEquals(19.25, $x4_with_fees['effective_annual_percentage_rate']);
            $this->assertEquals(17.74, $x4_with_fees['nominal_annual_percentage_rate']);
            $this->assertEquals(732, $x4_with_fees['total_cost']);

            $installments_x4 = $x4_with_fees['installments'];

            $this->assertEquals(3, count($installments_x4));

            $this->assertEquals('2020-03-06T01:00:00.000Z', $installments_x4[0]['date']);
            $this->assertEquals(16834, $installments_x4[0]['amount']);
            $this->assertEquals('2020-04-06T00:00:00.000Z', $installments_x4[1]['date']);
            $this->assertEquals(16833, $installments_x4[1]['amount']);
            $this->assertEquals('2020-05-06T00:00:00.000Z', $installments_x4[2]['date']);
            $this->assertEquals(16833, $installments_x4[2]['amount']);

            $this->assertEquals(17565, $x4_with_fees['down_payment_amount']);
        }
    }
}
