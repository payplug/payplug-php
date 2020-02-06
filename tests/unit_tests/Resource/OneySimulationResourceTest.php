<?php
namespace Payplug\Resource;
use Payplug;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class OneySimulationResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOneySimulation3xWithFees()
    {
        $simulation = OneySimulationResource::fromAttributes(array(
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
            )
        ));

        $this->assertObjectHasAttribute('x3_with_fees', $simulation);
        $this->assertEquals(true, is_array($simulation->x3_with_fees));
        $this->assertEquals(19.25, $simulation->x3_with_fees['effective_annual_percentage_rate']);
        $this->assertEquals(17.74, $simulation->x3_with_fees['nominal_annual_percentage_rate']);
        $this->assertEquals(732, $simulation->x3_with_fees['total_cost']);

        $installments = $simulation->x3_with_fees['installments'];

        $this->assertEquals(2, count($installments));
        foreach($installments as $installment) {
            $date = date('Y-m-d', strtotime($installment['date']));
            $this->assertNotEquals('1970-01-01', $date);
            $this->assertEquals(true, is_int($installment['amount']));
        }

        $this->assertEquals(17565, $simulation->x3_with_fees['down_payment_amount']);
    }

    public function testGetOneySimulation4xWithFees()
    {
        $simulation = OneySimulationResource::fromAttributes(array(
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
        ));

        $this->assertObjectHasAttribute('x4_with_fees', $simulation);
        $this->assertEquals(true, is_array($simulation->x4_with_fees));
        $this->assertEquals(19.25, $simulation->x4_with_fees['effective_annual_percentage_rate']);
        $this->assertEquals(17.74, $simulation->x4_with_fees['nominal_annual_percentage_rate']);
        $this->assertEquals(732, $simulation->x4_with_fees['total_cost']);

        $installments = $simulation->x4_with_fees['installments'];

        $this->assertEquals(3, count($installments));
        foreach($installments as $installment) {
            $date = date('Y-m-d', strtotime($installment['date']));
            $this->assertNotEquals('1970-01-01', $date);
            $this->assertEquals(true, is_int($installment['amount']));
        }

        $this->assertEquals(17565, $simulation->x4_with_fees['down_payment_amount']);
    }

    public function testGetOneySimulationWithFees()
    {
        $simulation = OneySimulationResource::fromAttributes(array(
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
        ));

        // check 3x with fees
        $this->assertObjectHasAttribute('x3_with_fees', $simulation);
        $this->assertEquals(true, is_array($simulation->x3_with_fees));
        $this->assertEquals(19.25, $simulation->x3_with_fees['effective_annual_percentage_rate']);
        $this->assertEquals(17.74, $simulation->x3_with_fees['nominal_annual_percentage_rate']);
        $this->assertEquals(732, $simulation->x3_with_fees['total_cost']);

        $installments = $simulation->x3_with_fees['installments'];

        $this->assertEquals(2, count($installments));
        foreach($installments as $installment) {
            $date = date('Y-m-d', strtotime($installment['date']));
            $this->assertNotEquals('1970-01-01', $date);
            $this->assertEquals(true, is_int($installment['amount']));
        }

        $this->assertEquals(17565, $simulation->x3_with_fees['down_payment_amount']);

        // check 4x with fees
        $this->assertObjectHasAttribute('x4_with_fees', $simulation);
        $this->assertEquals(true, is_array($simulation->x4_with_fees));
        $this->assertEquals(19.25, $simulation->x4_with_fees['effective_annual_percentage_rate']);
        $this->assertEquals(17.74, $simulation->x4_with_fees['nominal_annual_percentage_rate']);
        $this->assertEquals(732, $simulation->x4_with_fees['total_cost']);

        $installments = $simulation->x4_with_fees['installments'];

        $this->assertEquals(3, count($installments));
        foreach($installments as $installment) {
            $date = date('Y-m-d', strtotime($installment['date']));
            $this->assertNotEquals('1970-01-01', $date);
            $this->assertEquals(true, is_int($installment['amount']));
        }

        $this->assertEquals(17565, $simulation->x4_with_fees['down_payment_amount']);
    }
}
