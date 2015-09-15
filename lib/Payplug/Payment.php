<?php
namespace Payplug;

/**
 * The Payment DAO use to simplify the access to most usefull static methods
 **/
class Payment
{
	/**
     * Retrieves a Payment.
     *
     * @param   string                      $paymentId      the payment ID
     * @param   \Payplug\Payplug $payplug  the client configuration
     *
     * @return  null|\Payplug\Resource\Payment the retrieved payment or null on error
     *
     * @throws  \Payplug\Exception\ConfigurationNotSetException
     */
    public static function retrieve($paymentId, \Payplug\Payplug $payplug = null)
    {
    	return \Payplug\Resource\Payment::retrieve($paymentId, $payplug);
    }

    /**
     * Creates a Payment.
     *
     * @param   array                       $data           API data for payment creation
     * @param   \Payplug\Payplug $payplug  the client configuration
     *
     * @return  null|\Payplug\Resource\Payment the created payment instance
     *
     * @throws  PayPlug\Exception\ConfigurationNotSetException
     */
    public static function create(array $data, \Payplug\Payplug $payplug = null)
    {
    	return \Payplug\Resource\Payment::create($data, $payplug);
    }

    /**
     * List payments.
     *
     * @param   \Payplug\Payplug   $payplug  the client configuration
     * 
     * @return  null|\Payplug\Resource\Payment[]   the array of payments
     *
     * @throws  \Payplug\Exception\InvalidPaymentException
     * @throws  \Payplug\Exception\UnexpectedAPIResponseException
     */
    public static function listPayments($perPage = null, $page = null, \Payplug\Payplug $payplug = null)
    {
    	return \Payplug\Resource\Payment::listPayments($perPage, $page, $payplug);
    }    
};