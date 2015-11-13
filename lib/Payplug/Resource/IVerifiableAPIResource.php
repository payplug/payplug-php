<?php
namespace Payplug\Resource;
use Payplug;

/**
 * A verifiable API Resource is an API Resource that can be converted into a consistent object.
 * Typically, you need to verify a resource when you received it from a untrustworthy source (e.g. from a
 * notification).
 */
interface IVerifiableAPIResource
{
    /**
     * Returns an API resource that you can trust.
     *
     * @param   Payplug\Payplug $payplug  the client configuration.
     *
     * @return  Payplug\Resource\APIResource The consistent API resource.
     *
     * @throws  Payplug\Exception\UndefinedAttributeException when the local resource is invalid.
     */
    function getConsistentResource(Payplug\Payplug $payplug = null);
}
