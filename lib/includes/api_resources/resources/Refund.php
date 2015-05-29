<?php

/**
 * A PayPlug_Payment refund.
 */
class PayPlug_Refund extends PayPlug_APIResource implements PayPlug_IVerifiableAPIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  PayPlug_Refund  The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PayPlug_Refund();
        $object->initialize($attributes);
        return $object;
    }

    /**
     * Creates a refund on a payment.
     *
     * @param   string|PayPlug_Payment      $payment        the payment id or the payment object
     * @param   array                       $data           API data for refund
     * @param   PayPlug_ClientConfiguration $configuration  the client configuration
     *
     * @return  null|PayPlug_Refund the refund object
     * @throws  PayPlug_ConfigurationNotSetException
     */
    public static function create($payment, array $data = null, PayPlug_ClientConfiguration $configuration = null)
    {
        if ($configuration === null) {
            $configuration = PayPlug_ClientConfiguration::getDefaultConfiguration();
        }
        if ($payment instanceof PayPlug_Payment) {
            $payment = $payment->id;
        }

        $httpClient = new PayPlug_HttpClient($configuration);
        $response = $httpClient->post(
            PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::CREATE_REFUND, array('PAYMENT_ID' => $payment)),
            $data
        );

        return PayPlug_Refund::fromAttributes($response['httpResponse']);
    }

    /**
     * Retrieves a refund object on a payment.
     *
     * @param   string|PayPlug_Payment      $payment        the payment id or the payment object
     * @param   string                      $refundId       the refund id
     * @param   PayPlug_ClientConfiguration $configuration  the client configuration
     *
     * @return  null|PayPlug_APIResource|PayPlug_Refund the refund object
     *
     * @throws  PayPlug_ConfigurationNotSetException
     */
    public static function retrieve($payment, $refundId, PayPlug_ClientConfiguration $configuration = null)
    {
        if ($configuration === null) {
            $configuration = PayPlug_ClientConfiguration::getDefaultConfiguration();
        }
        if ($payment instanceof PayPlug_Payment) {
            $payment = $payment->id;
        }

        $httpClient = new PayPlug_HttpClient($configuration);
        $response = $httpClient->get(
            PayPlug_APIRoutes::getRoute(
                PayPlug_APIRoutes::RETRIEVE_REFUND,
                array(
                    'PAYMENT_ID' => $payment,
                    'REFUND_ID'  => $refundId
                )
            )
        );

        return PayPlug_Refund::fromAttributes($response['httpResponse']);
    }

    /**
     * Lists the last refunds of a payment.
     *
     * @param   string|PayPlug_Payment      $payment        the payment id or the payment object
     * @param   PayPlug_ClientConfiguration $configuration  the client configuration
     *
     * @return  null|PayPlug_Refund[]   an array containing the refunds on success.
     *
     * @throws PayPlug_ConfigurationNotSetException
     * @throws PayPlug_UnexpectedAPIResponseException
     */
    public static function listRefunds($payment, PayPlug_ClientConfiguration $configuration = null)
    {
        if ($configuration === null) {
            $configuration = PayPlug_ClientConfiguration::getDefaultConfiguration();
        }
        if ($payment instanceof PayPlug_Payment) {
            $payment = $payment->id;
        }

        $httpClient = new PayPlug_HttpClient($configuration);
        $response = $httpClient->get(
            PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::LIST_REFUNDS, array('PAYMENT_ID' => $payment))
        );

        if (!array_key_exists('data', $response['httpResponse']) || !is_array($response['httpResponse']['data'])) {
            throw new PayPlug_UnexpectedAPIResponseException(
                "Expected API response to contain 'data' key referencing an array.",
                $response['httpResponse']
            );
        }

        $refunds = array();
        foreach ($response['httpResponse']['data'] as &$refund) {
            $refunds[] = PayPlug_Refund::fromAttributes($refund);
        }

        return $refunds;
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
            throw new PayPlug_UndefinedAttributeException('The id of the refund is not set.');
        }
        else if (!array_key_exists('payment_id', $this->_attributes)) {
            throw new PayPlug_UndefinedAttributeException('The payment_id of the refund is not set.');
        }

        return PayPlug_Refund::retrieve($this->_attributes['payment_id'], $this->_attributes['id'], $configuration);
    }
}