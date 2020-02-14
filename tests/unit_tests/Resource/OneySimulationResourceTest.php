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
        $simulations = OneySimulationResource::fromAttributes(array(
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

        $this->assertObjectHasAttribute('x3_with_fees', $simulations);
        $this->assertEquals(true, is_array($simulations->x3_with_fees));
        $this->assertEquals(19.25, $simulations->x3_with_fees['effective_annual_percentage_rate']);
        $this->assertEquals(17.74, $simulations->x3_with_fees['nominal_annual_percentage_rate']);
        $this->assertEquals(732, $simulations->x3_with_fees['total_cost']);

        $installments = $simulations->x3_with_fees['installments'];

        $this->assertEquals(2, count($installments));

        $this->assertEquals('2020-03-06T01:00:00.000Z', $installments[0]['date']);
        $this->assertEquals(16834, $installments[0]['amount']);
        $this->assertEquals('2020-04-06T00:00:00.000Z', $installments[1]['date']);
        $this->assertEquals(16833, $installments[1]['amount']);

        $this->assertEquals(17565, $simulations->x3_with_fees['down_payment_amount']);
    }

    public function testGetOneySimulation4xWithFees()
    {
        $simulations = OneySimulationResource::fromAttributes(array(
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

        $this->assertObjectHasAttribute('x4_with_fees', $simulations);
        $this->assertEquals(true, is_array($simulations->x4_with_fees));
        $this->assertEquals(19.25, $simulations->x4_with_fees['effective_annual_percentage_rate']);
        $this->assertEquals(17.74, $simulations->x4_with_fees['nominal_annual_percentage_rate']);
        $this->assertEquals(732, $simulations->x4_with_fees['total_cost']);

        $installments = $simulations->x4_with_fees['installments'];

        $this->assertEquals(3, count($installments));

        $this->assertEquals('2020-03-06T01:00:00.000Z', $installments[0]['date']);
        $this->assertEquals(16834, $installments[0]['amount']);
        $this->assertEquals('2020-04-06T00:00:00.000Z', $installments[1]['date']);
        $this->assertEquals(16833, $installments[1]['amount']);
        $this->assertEquals('2020-05-06T00:00:00.000Z', $installments[2]['date']);
        $this->assertEquals(16833, $installments[2]['amount']);

        $this->assertEquals(17565, $simulations->x4_with_fees['down_payment_amount']);
    }

    public function testGetOneySimulationWithFees()
    {
        $simulations = OneySimulationResource::fromAttributes(array(
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
        $this->assertObjectHasAttribute('x3_with_fees', $simulations);
        $this->assertEquals(true, is_array($simulations->x3_with_fees));
        $this->assertEquals(19.25, $simulations->x3_with_fees['effective_annual_percentage_rate']);
        $this->assertEquals(17.74, $simulations->x3_with_fees['nominal_annual_percentage_rate']);
        $this->assertEquals(732, $simulations->x3_with_fees['total_cost']);

        $installments_x3 = $simulations->x3_with_fees['installments'];

        $this->assertEquals(2, count($installments_x3));
        $this->assertEquals('2020-03-06T01:00:00.000Z', $installments_x3[0]['date']);
        $this->assertEquals(16834, $installments_x3[0]['amount']);
        $this->assertEquals('2020-04-06T00:00:00.000Z', $installments_x3[1]['date']);
        $this->assertEquals(16833, $installments_x3[1]['amount']);

        $this->assertEquals(17565, $simulations->x3_with_fees['down_payment_amount']);

        // check 4x with fees
        $this->assertObjectHasAttribute('x4_with_fees', $simulations);
        $this->assertEquals(true, is_array($simulations->x4_with_fees));
        $this->assertEquals(19.25, $simulations->x4_with_fees['effective_annual_percentage_rate']);
        $this->assertEquals(17.74, $simulations->x4_with_fees['nominal_annual_percentage_rate']);
        $this->assertEquals(732, $simulations->x4_with_fees['total_cost']);

        $installments_x4 = $simulations->x4_with_fees['installments'];

        $this->assertEquals(3, count($installments_x4));

        $this->assertEquals('2020-03-06T01:00:00.000Z', $installments_x4[0]['date']);
        $this->assertEquals(16834, $installments_x4[0]['amount']);
        $this->assertEquals('2020-04-06T00:00:00.000Z', $installments_x4[1]['date']);
        $this->assertEquals(16833, $installments_x4[1]['amount']);
        $this->assertEquals('2020-05-06T00:00:00.000Z', $installments_x4[2]['date']);
        $this->assertEquals(16833, $installments_x4[2]['amount']);

        $this->assertEquals(17565, $simulations->x4_with_fees['down_payment_amount']);
    }
}
