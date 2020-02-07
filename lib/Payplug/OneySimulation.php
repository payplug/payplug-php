<?php
namespace Payplug;

/**
 * Getting Oney Payment Simulation from MArketing API
 **/
class OneySimulation {
    /**
     * Get Oney Payment Simulation
     *
     * @param $data
     * @param Payplug|null $payplug
     * @return array
     * @throws Exception\ConfigurationNotSetException
     * @throws Exception\ConnectionException
     * @throws Exception\HttpException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public static function getSimulations($data, Payplug $payplug = null)
    {
        return Resource\OneySimulationResource::getSimulations($data, $payplug);
    }
}
