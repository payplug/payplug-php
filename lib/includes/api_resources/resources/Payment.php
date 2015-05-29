<?php

/**
 * A Payment
 */
class PayPlug_Payment extends PayPlug_APIResource implements PayPlug_IVerifiableAPIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  PayPlug_Payment The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PayPlug_Payment();
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
            $this->card = PayPlug_Card::fromAttributes($attributes['card']);
        }
        if (isset($attributes['customer'])) {
            $this->customer = PayPlug_Customer::fromAttributes($attributes['customer']);
        }
        if (isset($attributes['hosted_payment'])) {
            $this->hosted_payment = PayPlug_HostedPayment::fromAttributes($attributes['hosted_payment']);
        }
        if (isset($attributes['failure'])) {
            $this->failure = PayPlug_PaymentFailure::fromAttributes($attributes['failure']);
        }
    }

    /**
     * Open a refund on the payment.
     *
     * @param   array                       $data           the refund data
     * @param   PayPlug_ClientConfiguration $configuration  the client configuration
     *
     * @return  PayPlug_Refund|null the opened refund instance
     *
     * @throws  PayPlug_InvalidPaymentException when the id of the payment is invalid
     */
    public function refund(array $data = null, PayPlug_ClientConfiguration $configuration = null)
    {
        if (!array_key_exists('id', $this->getAttributes())) {
            throw new PayPlug_InvalidPaymentException("This payment object has no id. It can't be refunded.");
        }

        return PayPlug_Refund::create($this->id, $data, $configuration);
    }

    /**
     * List the refunds of this payment.
     *
     * @param   PayPlug_ClientConfiguration   $configuration  the client configuration
     * 
     * @return  null|PayPlug_Refund[]   the array of refunds of this payment
     *
     * @throws  PayPlug_InvalidPaymentException
     * @throws  PayPlug_UnexpectedAPIResponseException
     */
    public function listRefunds(PayPlug_ClientConfiguration $configuration = null)
    {
        if (!array_key_exists('id', $this->getAttributes())) {
            throw new PayPlug_InvalidPaymentException("This payment object has no id. You can't list refunds on it.");
        }

        return PayPlug_Refund::listRefunds($this->id, $configuration);
    }

    /**
     * Retrieves a Payment.
     *
     * @param   string                      $paymentId      the payment ID
     * @param   PayPlug_ClientConfiguration $configuration  the client configuration
     *
     * @return  null|PayPlug_Payment the retrieved payment or null on error
     *
     * @throws  PayPlug_ConfigurationNotSetException
     */
    public static function retrieve($paymentId, PayPlug_ClientConfiguration $configuration = null)
    {
        if ($configuration === null) {
            $configuration = PayPlug_ClientConfiguration::getDefaultConfiguration();
        }

        $httpClient = new PayPlug_HttpClient($configuration);
        $response = $httpClient->get(
            PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::RETRIEVE_PAYMENT, array('PAYMENT_ID' => $paymentId))
        );

        return PayPlug_Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * Creates a Payment.
     *
     * @param   array                       $data           API data for payment creation
     * @param   PayPlug_ClientConfiguration $configuration  the client configuration
     *
     * @return  null|PayPlug_Payment the created payment instance
     *
     * @throws  PayPlug_ConfigurationNotSetException
     */
    public static function create(array $data, PayPlug_ClientConfiguration $configuration = null)
    {
        if ($configuration === null) {
            $configuration = PayPlug_ClientConfiguration::getDefaultConfiguration();
        }

        $httpClient = new PayPlug_HttpClient($configuration);
        $response = $httpClient->post(
            PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::CREATE_PAYMENT),
            $data
        );

        return PayPlug_Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * Returns an API resource that you can trust.
     *
     * @param   PayPlug_ClientConfiguration $configuration the client configuration.
     *
     * @return  PayPlug_APIResource The consistent API resource.
     *
     * @throws  PayPlug_UndefinedAttributeException when the local resource is invalid.
     */
    function getConsistentResource(PayPlug_ClientConfiguration $configuration = null)
    {
        if (!array_key_exists('id', $this->_attributes)) {
            throw new PayPlug_UndefinedAttributeException('The id of the payment is not set.');
        }

        return PayPlug_Payment::retrieve($this->_attributes['id'], $configuration);
    }
}