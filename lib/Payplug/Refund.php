<?php
namespace Payplug;

/**
 * The Refund DAO use to simplify the access to most usefull static methods
 **/
class Refund {
	/**
     * Creates a refund on a payment.
     *
     * @param   string|Payment      $payment        the payment id or the payment object
     * @param   array                       $data           API data for refund
     * @param   \Payplug\Payplug $payplug  the client configuration
     *
     * @return  null|Refund the refund object
     * @throws  \Payplug\Exception\ConfigurationNotSetException
     */
    public static function create($payment, array $data = null, \Payplug\Payplug $payplug = null)
    {
    	return \Payplug\Resource\Refund::create($payment, $data, $payplug);
    }

    /**
     * Retrieves a refund object on a payment.
     *
     * @param   string|Payment      $payment        the payment id or the payment object
     * @param   string                      $refundId       the refund id
     * @param   \Payplug\Payplug $payplug  the client configuration
     *
     * @return  null|\Payplug\APIResource|Refund the refund object
     *
     * @throws  \Payplug\Exception\ConfigurationNotSetException
     */
    public static function retrieve($payment, $refundId, \Payplug\Payplug $payplug = null)
    {
    	return \Payplug\Resource\Refund::retrieve($payment, $refundId, $payplug);
    }

    /**
     * Lists the last refunds of a payment.
     *
     * @param   string|Payment      $payment        the payment id or the payment object
     * @param   \Payplug\Payplug $payplug  the client configuration
     *
     * @return  null|Refund[]   an array containing the refunds on success.
     *
     * @throws \Payplug\Exception\ConfigurationNotSetException
     * @throws \Payplug\Exception\UnexpectedAPIResponseException
     */
    public static function listRefunds($payment, $perPage = null, $page = null, \Payplug\Payplug $payplug = null)
    {
    	return \Payplug\Resource\Refund::listRefunds($payment, $perPage, $page, $payplug);
    }
}