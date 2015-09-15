<?php
namespace Payplug;

/**
 * Handles PayPlug's notifications easily.
 */
class Notification
{
    /**
     * This function treats a notification and verifies its authenticity.
     *
     * @param   string                      $requestBody    JSON Data sent by the notifier.
     * @param   \Payplug\Payplug $authentication  The client configuration.
     *
     * @return  \Payplug\IVerifiableAPIResource  A safe API Resource.
     *
     * @throws  \Payplug\Exception\UnknownAPIResourceException
     */
    public static function treat($requestBody, $authentication = null)
    {
        $postArray = json_decode($requestBody, true);

        if ($postArray === null) {
            throw new \Payplug\Exception\UnknownAPIResourceException('Request body is not valid JSON.');
        }

        $unsafeAPIResource = \Payplug\Resource\APIResource::factory($postArray);
        return $unsafeAPIResource->getConsistentResource($authentication);
    }
}
