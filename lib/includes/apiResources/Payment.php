<?php

/**
 * A Payment
 */
class PayPlug_Payment extends PayPlug_APIResource
{
    /**
     * {@inheritdoc}
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PayPlug_Payment();
        $object->initialize($attributes);
        return $object;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(array $attributes)
    {
        parent::initialize($attributes);

        if (isset($attributes['card'])) {
            $this->setAttribute('card', PayPlug_Card::fromAttributes($attributes['card']));
        }
        if (isset($attributes['customer'])) {
            $this->setAttribute('customer', PayPlug_Customer::fromAttributes($attributes['customer']));
        }
        if (isset($attributes['hosted_payment'])) {
            $this->setAttribute('hosted_payment', PayPlug_HostedPayment::fromAttributes($attributes['hosted_payment']));
        }
        if (isset($attributes['failure'])) {
            $this->setAttribute('failure', PayPlug_PaymentFailure::fromAttributes($attributes['failure']));
        }
    }

    /**
     * Open a refund on the payment.
     * @param array $data the refund data
     * @param PayPlug_ClientConfiguration $configuration the client configuration
     * @return PayPlug_Refund|null the opened refund instance
     * @throws PayPlug_ConfigurationNotSetException
     */
    public function refund(array $data = null, PayPlug_ClientConfiguration $configuration = null)
    {
        if (!array_key_exists('id', $this->getAttributes())) return null;
        return PayPlug_Refund::create($this->getAttribute('id'), $data, $configuration);
    }

    /**
     * Retrieves a Payment.
     * @param string $paymentId the payment ID
     * @param PayPlug_ClientConfiguration $configuration the client configuration
     * @return null|PayPlug_Payment the retrieved payment or null on error
     * @throws PayPlug_ConfigurationNotSetException
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
     * Creates a Payment
     * @param array $data API data for payment creation
     * @param PayPlug_ClientConfiguration $configuration the client configuration
     * @return null|PayPlug_Payment the created payment instance
     * @throws PayPlug_ConfigurationNotSetException
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
}