<?php

namespace Payplug\Resource;
use Payplug;

/**
 * A Oney Payment Simulation
 */
class OneySimulationResource extends APIResource
{

    public $x3_with_fees;
    public $x4_with_fees;

    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  OneySimulationResource The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new OneySimulationResource();
        $object->initialize($attributes);
        return $object;
    }

    /**
     * Initializes the resource.
     * This method must be overridden when the resource has objects as attributes.
     *
     * @param   array   $attributes the attributes to initialize.
     */
    protected function initialize(array $attributes)
    {
        parent::initialize($attributes);

        // initialize Oney Payment Simulation with x3_with_fees operation
        if (isset($attributes['x3_with_fees'])) {
            $this->x3_with_fees = $attributes['x3_with_fees'];
        }

        // initialize Oney Payment Simulation with x4_with_fees operation
        if (isset($attributes['x4_with_fees'])) {
            $this->x4_with_fees = $attributes['x4_with_fees'];
        }
    }

    /**
     * Get Oney Payment Simulation
     *
     * @param $sim_data
     * @param Payplug\Payplug|null $payplug
     * @return array
     * @throws Payplug\Exception\ConfigurationNotSetException
     * @throws Payplug\Exception\ConnectionException
     * @throws Payplug\Exception\HttpException
     * @throws Payplug\Exception\UnexpectedAPIResponseException
     */
    public static function getSimulations($sim_data, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $sim_route = Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::ONEY_PAYMENT_SIM_RESOURCE);

        $httpClient = new Payplug\Core\HttpClient($payplug);

        $response = $httpClient->post(
            $sim_route,
            $sim_data
        );

        return $response['httpResponse'];
    }
}
