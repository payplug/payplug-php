<?php

/**
 * A verifiable API Resource is an API Resource that can be converted into a consistent object.
 * Typically, you need to verify a resource when you received it from a untrustworthy source (e.g. from a
 * notification).
 */
interface PayPlug_IVerifiableAPIResource
{
    /**
     * Returns an API resource that you can trust.
     *
     * @param   PayPlug_ClientConfiguration $configuration  the client configuration.
     *
     * @return  PayPlug_APIResource The consistent API resource.
     *
     * @throws  PayPlug_UndefinedAttributeException when the local resource is invalid.
     */
    function getConsistentResource(PayPlug_ClientConfiguration $configuration = null);
}