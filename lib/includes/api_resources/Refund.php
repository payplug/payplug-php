<?php

/**
 * A PayPlug_Payment refund.
 */
class PayPlug_Refund extends PayPlug_APIResource
{
    /**
     * {@inheritdoc}
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PayPlug_Refund();
        $object->initialize($attributes);
        return $object;
    }

    /**
     * Creates a refund on a payment.
     * @param string|PayPlug_Payment $payment the payment id or the payment object
     * @param array $data API data for refund
     * @param PayPlug_ClientConfiguration $configuration the client configuration
     * @return null|PayPlug_Refund the refund object or null on error
     * @throws PayPlug_ConfigurationNotSetException
     */
    public static function create($payment, array $data = null, PayPlug_ClientConfiguration $configuration = null)
    {
        if ($configuration === null) {
            $configuration = PayPlug_ClientConfiguration::getDefaultConfiguration();
        }
        if ($payment instanceof PayPlug_Payment) {
            $payment = $payment->getAttribute('id');
        }

        $httpClient = new PayPlug_HttpClient($configuration);
        $response = $httpClient->post(
            PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::CREATE_REFUND, array('PAYMENT_ID' => $payment)),
            $data
        );

        return PayPlug_Refund::fromAttributes($response['httpResponse']);
    }

    /**
     * Retrieves a refund object on a payment
     * @param string|PayPlug_Payment $payment the payment id or the payment object
     * @param $refundId
     * @param PayPlug_ClientConfiguration $configuration
     * @return null|PayPlug_APIResource|PayPlug_Refund
     * @throws PayPlug_ConfigurationNotSetException
     */
    public static function retrieve($payment, $refundId, PayPlug_ClientConfiguration $configuration = null)
    {
        if ($configuration === null) {
            $configuration = PayPlug_ClientConfiguration::getDefaultConfiguration();
        }
        if ($payment instanceof PayPlug_Payment) {
            $payment = $payment->getAttribute('id');
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
     * @param string|PayPlug_Payment $payment the payment id or the payment object
     * @param PayPlug_ClientConfiguration $configuration
     * @return PayPlug_Refund[]|null an array containing the refunds on success, null on error.
     * @throws PayPlug_ConfigurationNotSetException
     */
    public static function list_refunds($payment, PayPlug_ClientConfiguration $configuration = null)
    {
        if ($configuration === null) {
            $configuration = PayPlug_ClientConfiguration::getDefaultConfiguration();
        }
        if ($payment instanceof PayPlug_Payment) {
            $payment = $payment->getAttribute('id');
        }

        $httpClient = new PayPlug_HttpClient($configuration);
        $response = $httpClient->get(
            PayPlug_APIRoutes::getRoute(PayPlug_APIRoutes::LIST_REFUNDS, array('PAYMENT_ID' => $payment))
        );

        $refunds = array();
        assert(array_key_exists('data', $refunds));
        foreach ($response['httpResponse']['data'] as &$refund) {
            $refunds[] = PayPlug_Refund::fromAttributes($refund);
        }
        return $refunds;
    }
}