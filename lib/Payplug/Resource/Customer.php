<?php
namespace Payplug\Resource;
use Payplug;

/**
 * A Customer
 */
class Customer extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  Customer The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new Customer();
        $object->initialize($attributes);
        return $object;
    }

    /**
     * Create a customer.
     *
     * @param   array $data API data for customer creation
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @return  null|Customer the created Customer instance
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function create(array $data, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->post(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::CUSTOMER_RESOURCE),
            $data
        );

        return Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * Retrieve a customer.
     *
     * @param   array $customerId The ID of the customer to retrieve
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @return  Customer the retrieved Customer instance
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function retrieve($customerId, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->get(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::CUSTOMER_RESOURCE, $customerId)
        );

        return Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * Update a customer.
     *
     * @param   array $data API data for customer update
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @return  null|Customer the new Customer instance
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public function update(array $data, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->patch(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::CUSTOMER_RESOURCE, $this->id),
            $data
        );

        return Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * Delete a customer.
     *
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public function delete(Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $httpClient->delete(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::CUSTOMER_RESOURCE, $this->id)
        );
    }

    /**
     * List customers.
     *
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @param   int $perPage the number of results per page
     * @param   int $page the page number
     * @return  Customer[] the array of payments
     *
     * @throws  Payplug\Exception\InvalidPaymentException
     * @throws  Payplug\Exception\UnexpectedAPIResponseException
     */
    public static function listCustomers($perPage = null, $page = null, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $pagination = array('per_page' => $perPage, 'page' => $page);
        $response = $httpClient->get(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::CUSTOMER_RESOURCE, null, array(), $pagination)
        );

        if (!array_key_exists('data', $response['httpResponse'])
            || !is_array($response['httpResponse']['data'])) {
            throw new Payplug\Exception\UnexpectedAPIResponseException(
                "Expected 'data' key in API response.",
                $response['httpResponse']
            );
        }

        $customers = array();
        foreach ($response['httpResponse']['data'] as &$customer) {
            $customers[] = Customer::fromAttributes($customer);
        }

        return $customers;
    }

    /**
     * List the cards of this customer.
     *
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @param   int $perPage the number of results per page
     * @param   int $page the page number
     * @return  Card[] the array of cards
     *
     * @throws  Payplug\Exception\InvalidPaymentException
     * @throws  Payplug\Exception\UnexpectedAPIResponseException
     */
    public function listCards($perPage = null, $page = null, Payplug\Payplug $payplug = null)
    {
        return Payplug\Resource\Card::listCards($this, $perPage, $page, $payplug);
    }

    /**
     * Add a card to this customer.
     *
     * @param   array $data the card data
     * @param   Payplug\Payplug $payplug the client configuration
     *
     * @return  Card the created card object
     *
     * @throws  Payplug\Exception\InvalidPaymentException
     * @throws  Payplug\Exception\UnexpectedAPIResponseException
     */
    public function addCard($data, Payplug\Payplug $payplug = null)
    {
        return Payplug\Resource\Card::create($this, $data, $payplug);
    }
}
