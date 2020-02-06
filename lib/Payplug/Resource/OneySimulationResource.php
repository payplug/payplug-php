<?php

namespace Payplug\Resource;
use Payplug;

/**
 * A Oney Payment Simulation
 */
class OneySimulationResource
{
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
    public static function get($sim_data, Payplug\Payplug $payplug = null)
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
