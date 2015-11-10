<?php
namespace Payplug\Resource;

/**
 * Interface designed to force resources to implement at least one factory.
 */
interface IAPIResourceFactory
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  APIResource The new resource.
     */
    static function fromAttributes(array $attributes);
}
