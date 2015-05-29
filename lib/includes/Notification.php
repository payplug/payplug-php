<?php

/**
 * Handles PayPlug's notifications easily.
 */
class PayPlug_Notification
{
    /**
     * This function treats a notification and verifies its authenticity.
     *
     * @param   string                      $postData       JSON Data sent by the notifier.
     * @param   PayPlug_ClientConfiguration $configuration  The client configuration.
     *
     * @return  PayPlug_IVerifiableAPIResource  A safe API Resource.
     *
     * @throws  PayPlug_UnknownAPIResourceException
     */
    public static function treat($postData, $configuration = null)
    {
        $postArray = json_decode($postData, true);

        if ($postArray === null) {
            throw new PayPlug_UnknownAPIResourceException('Given POST data is not valid JSON.');
        }

        $unsafeAPIResource = PayPlug_APIResource::factory($postArray);
        return $unsafeAPIResource->getConsistentResource($configuration);
    }
}