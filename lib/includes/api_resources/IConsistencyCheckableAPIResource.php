<?php

/**
 * A Consistency Checkable API Resource is an API Resource from which you can retrieve a consistent object.
 * Typically, you need to validate a resource when you received it from a untrustworthy source (e.g. from a
 * notification).
 */
interface PayPlug_IConsistencyCheckableAPIResource
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