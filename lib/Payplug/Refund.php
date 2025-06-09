<?php
namespace Payplug;

/**
 * The Refund DAO simplifies the access to most useful methods
 **/
class Refund {
	/**
     * Creates a refund on a payment.
     *
     * @param   string|Payment      $payment        the payment id or the payment object
     * @param   array                       $data           API data for refund
     * @param   Payplug $payplug  the client configuration
     * @param $is_hosted_field indicates if the payment is using hosted fields
     *
     * @return  null|Refund the refund object
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function create($payment, array $data = null, Payplug $payplug = null,  $is_hosted_field = false)
    {
        return Resource\Refund::create($payment, $data, $payplug,  $is_hosted_field);
    }

    /**
     * Retrieves a refund object on a payment.
     *
     * @param   string|Payment      $payment        the payment id or the payment object
     * @param   string                      $refundId       the refund id
     * @param   Payplug $payplug  the client configuration
     *
     * @return  null|Resource\APIResource|Refund the refund object
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function retrieve($payment, $refundId, $payplug = null)
    {
    	return Resource\Refund::retrieve($payment, $refundId, $payplug);
    }

    /**
     * Lists the last refunds of a payment.
     *
     * @param   string|Payment      $payment        the payment id or the payment object
     * @param   Payplug $payplug  the client configuration
     *
     * @return  null|Refund[]   an array containing the refunds on success.
     *
     * @throws Exception\ConfigurationNotSetException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public static function listRefunds($payment, $payplug = null)
    {
    	return Resource\Refund::listRefunds($payment, $payplug);
    }
}