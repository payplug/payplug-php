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
     * Create a card.
     *
     * @param   Customer|string $customer The customer object or id
     * @param   array $data API data for customer creation
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @return  null|Card the created card
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function create($customer, array $data, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }
        if ($customer instanceof Customer) {
            $customer = $customer->id;
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->post(
            Payplug\Core\APIRoutes::getRoute(
                Payplug\Core\APIRoutes::CARD_RESOURCE, null, array('CUSTOMER_ID' => $customer)
            ),
            $data
        );

        return Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * Retrieve a card object on a customer.
     *
     * @param   string|Customer $customer the customer id or the customer object
     * @param   string $cardId the card id
     * @param   Payplug\Payplug $payplug  the client configuration
     *
     * @return  null|Payplug\Resource\APIResource|Card the card object
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function retrieve($customer, $cardId, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }
        if ($customer instanceof Customer) {
            $customer = $customer->id;
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->get(
            Payplug\Core\APIRoutes::getRoute(
                Payplug\Core\APIRoutes::CARD_RESOURCE, $cardId, array('CUSTOMER_ID' => $customer)
            )
        );

        return Refund::fromAttributes($response['httpResponse']);
    }

    /**
     * Delete a card.
     *
     * @param   string|Customer $customer the customer id or the customer object
     * @param   string|Card $card the card id or the card object
     * @param   Payplug\Payplug $payplug  the client configuration
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function deleteCard($customer, $card, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }
        if ($customer instanceof Customer) {
            $customer = $customer->id;
        }
        if ($card instanceof Card) {
            $card = $card->id;
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $httpClient->delete(
            Payplug\Core\APIRoutes::getRoute(
                Payplug\Core\APIRoutes::CARD_RESOURCE, $card, array('CUSTOMER_ID' => $customer)
            )
        );
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
        self::deleteCard($this->customer_id, $this->id, $payplug);
    }

    /**
     * List the cards of a customer.
     *
     * @param   string|Customer $customer the customer id or the customer object
     * @param   int $perPage the number of results per page
     * @param   int $page the page number
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @return  Card[] an array containing the cards.
     *
     * @throws Payplug\Exception\ConfigurationNotSetException
     * @throws Payplug\Exception\UnexpectedAPIResponseException
     */
    public static function listCards($customer, $perPage = null, $page = null, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }
        if ($customer instanceof Customer) {
            $customer = $customer->id;
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);

        $pagination = array('per_page' => $perPage, 'page' => $page);
        $response = $httpClient->get(
            Payplug\Core\APIRoutes::getRoute(
                Payplug\Core\APIRoutes::CARD_RESOURCE, null, array('CUSTOMER_ID' => $customer), $pagination)
        );

        if (!array_key_exists('data', $response['httpResponse']) || !is_array($response['httpResponse']['data'])) {
            throw new Payplug\Exception\UnexpectedAPIResponseException(
                "Expected API response to contain 'data' key referencing an array.",
                $response['httpResponse']
            );
        }

        $cards = array();
        foreach ($response['httpResponse']['data'] as &$card) {
            $cards[] = Card::fromAttributes($card);
        }

        return $cards;
    }
}
