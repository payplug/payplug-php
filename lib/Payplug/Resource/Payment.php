<?php
namespace Payplug\Resource;

/**
 * A Payment
 */
class Payment extends APIResource implements IVerifiableAPIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  Payment The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new Payment();
        $object->initialize($attributes);
        return $object;
    }

    /**
     * Initializes the resource.
     * This method must be overridden when the resource has objects as attributes.
     *
     * @param   array   $attributes the attributes to initialize.
     */
    protected function initialize(array $attributes)
    {
        parent::initialize($attributes);

        if (isset($attributes['card'])) {
            $this->card = Card::fromAttributes($attributes['card']);
        }
        if (isset($attributes['customer'])) {
            $this->customer = Customer::fromAttributes($attributes['customer']);
        }
        if (isset($attributes['hosted_payment'])) {
            $this->hosted_payment = HostedPayment::fromAttributes($attributes['hosted_payment']);
        }
        if (isset($attributes['failure'])) {
            $this->failure = PaymentFailure::fromAttributes($attributes['failure']);
        }
        if (isset($attributes['notification'])) {
            $this->notification = Notification::fromAttributes($attributes['notification']);
        }
    }

    /**
     * Open a refund on the payment.
     *
     * @param   array                       $data           the refund data
     * @param   \Payplug\Payplug $payplug  the client configuration
     *
     * @return  Refund|null the opened refund instance
     *
     * @throws  \Payplug\Exception\InvalidPaymentException when the id of the payment is invalid
     */
    public function refund(array $data = null, \Payplug\Payplug $payplug = null)
    {
        if (!array_key_exists('id', $this->getAttributes())) {
            throw new \Payplug\Exception\InvalidPaymentException("This payment object has no id. It can't be refunded.");
        }

        return Refund::create($this->id, $data, $payplug);
    }

    /**
     * List the refunds of this payment.
     *
     * @param   \Payplug\Payplug   $payplug  the client configuration
     * 
     * @return  null|Refund[]   the array of refunds of this payment
     *
     * @throws  \Payplug\Exception\InvalidPaymentException
     * @throws  \Payplug\Exception\UnexpectedAPIResponseException
     */
    public function listRefunds($perPage = null, $page = null, \Payplug\Payplug $payplug = null)
    {
        if (!array_key_exists('id', $this->getAttributes())) {
            throw new \Payplug\Exception\InvalidPaymentException("This payment object has no id. You can't list refunds on it.");
        }

        return Refund::listRefunds($this->id, $perPage, $page, $payplug);
    }

    /**
     * Retrieves a Payment.
     *
     * @param   string                      $paymentId      the payment ID
     * @param   \Payplug\Payplug $payplug  the client configuration
     *
     * @return  null|Payment the retrieved payment or null on error
     *
     * @throws  \Payplug\Exception\ConfigurationNotSetException
     */
    public static function retrieve($paymentId, \Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = \Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new \Payplug\Core\HttpClient($payplug);
        $response = $httpClient->get(
            \Payplug\Core\APIRoutes::getRoute(\Payplug\Core\APIRoutes::RETRIEVE_PAYMENT, array('PAYMENT_ID' => $paymentId))
        );

        return Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * List payments.
     *
     * @param   \Payplug\Payplug   $payplug  the client configuration
     * 
     * @return  null|Payment[]   the array of payments
     *
     * @throws  \Payplug\Exception\InvalidPaymentException
     * @throws  \Payplug\Exception\UnexpectedAPIResponseException
     */
    public static function listPayments($perPage = null, $page = null, \Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = \Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new \Payplug\Core\HttpClient($payplug);
        $parameters = array();
        $pagination = array('per_page' => $perPage, 'page' => $page);
        $response = $httpClient->get(
            \Payplug\Core\APIRoutes::getRoute(\Payplug\Core\APIRoutes::LIST_PAYMENTS, $parameters, $pagination)
        );

        if (!array_key_exists('data', $response['httpResponse']) || !is_array($response['httpResponse']['data'])) {
            throw new \Payplug\Exception\UnexpectedAPIResponseException(
                "Expected API response to contain 'data' key referencing an array.",
                $response['httpResponse']
            );
        }

        $wrap = $response['httpResponse'];
        $payments = array();
        foreach ($response['httpResponse']['data'] as &$payment) {
            $payments[] = Payment::fromAttributes($payment);
        }
        $wrap['data'] = $payments;
        return $wrap;
    }

    /**
     * Creates a Payment.
     *
     * @param   array                       $data           API data for payment creation
     * @param   \Payplug\Payplug $payplug  the client configuration
     *
     * @return  null|Payment the created payment instance
     *
     * @throws  PayPlug\Exception\ConfigurationNotSetException
     */
    public static function create(array $data, \Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = \Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new \Payplug\Core\HttpClient($payplug);
        $response = $httpClient->post(
            \Payplug\Core\APIRoutes::getRoute(\Payplug\Core\APIRoutes::CREATE_PAYMENT),
            $data
        );

        return Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * Returns an API resource that you can trust.
     *
     * @param   \Payplug\Payplug $payplug the client configuration.
     *
     * @return  \Payplug\APIResource The consistent API resource.
     *
     * @throws  \Payplug\Exception\UndefinedAttributeException when the local resource is invalid.
     */
    function getConsistentResource(\Payplug\Payplug $payplug = null)
    {
        if (!array_key_exists('id', $this->_attributes)) {
            throw new \Payplug\Exception\UndefinedAttributeException('The id of the payment is not set.');
        }

        return Payment::retrieve($this->_attributes['id'], $payplug);
    }
}
