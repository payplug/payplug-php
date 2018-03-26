<?php
namespace Payplug\Resource;
use Payplug;

/**
 * A Card
 */
class Card extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  Card The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new Card();
        $object->initialize($attributes);
        return $object;
    }

    /**
     * Delete the card.
     *
     * @param   Payplug\Card $card  the card or card id
     * @param   Payplug\Payplug $payplug  the client configuration
     *
     * @return  Card the deleted card or null on error
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function deleteCard($card, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        if ($card instanceof Card) {
            $card = $card->id;
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->delete(Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::CARD_RESOURCE, $card));

        return $response;
    }

    /**
     * Delete the card.
     *
     * @param   Payplug\Payplug $payplug  the client configuration
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public function delete(Payplug\Payplug $payplug = null)
    {
        self::deleteCard($this->id, $payplug);
    }
}
