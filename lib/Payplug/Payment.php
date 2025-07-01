<?php
namespace Payplug;

/**
 * The Payment DAO simplifies the access to most useful methods
 **/
class Payment
{
	/**
     * Retrieves a Payment.
     *
     * @param   $data
     * @param   Payplug $payplug  the client configuration
     * @param $isHostedField
     *
     * @return  null|Resource\Payment the retrieved payment or null on error
     *
     * @throws  Exception\ConfigurationNotSetException
     */


    public static function retrieve($data, Payplug $payplug = null, $isHostedField = false)
    {
        return Resource\Payment::retrieve($data, $payplug, $isHostedField);
    }

	/**
     * Aborts a Payment.
     *
     * @param   string                      $paymentId      the payment ID
     * @param   Payplug $payplug  the client configuration
     *
     * @return  null|Resource\Payment the aborted payment or null on error
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function abort($paymentId, Payplug $payplug = null)
    {
        $payment = Resource\Payment::fromAttributes(array('id' => $paymentId));
    	return $payment->abort($payplug);
    }

    /**
     * Captures a Payment.
     *
     * @param   string                      $paymentId      the payment ID
     * @param   Payplug $payplug  the client configuration
     *
     * @return  null|Resource\Payment the captured payment or null on error
     *
     * @throws  Exception\ConfigurationNotSetException
     */

    /**
     * Capture a payment by its ID or data array.
     *
     * @param string|array $paymentId The payment ID as a string, or an array of payment data.
     * @param Payplug|null $payplug The client configuration (optional).
     * @return Resource\Payment|null The captured payment or null on error.
     * @throws Exception\ConfigurationNotSetException
     */
    public static function capture($paymentId, Payplug $payplug = null)
    {
        $payment = Resource\Payment::fromAttributes(array('id' => $paymentId));
        return $payment->capture($payplug);
    }

    /**
     * @description Authorize a Payment.
     * @param $data
     * @param Payplug|null $payplug
     * @param $is_hosted_field
     * @return mixed
     */
    public static  function authorize($data, Payplug $payplug = null, $is_hosted_field = false)
    {
        return Resource\Payment::authorize($data, $payplug, $is_hosted_field);

    }

    /**
     * Creates a Payment.
     *
     * @param   array                       $data           API data for payment creation
     * @param   Payplug $payplug  the client configuration
     *
     * @return  null|Resource\Payment the created payment instance
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function create(array $data, Payplug $payplug = null)
    {
        return Resource\Payment::create($data, $payplug);

    }

    /**
     * List payments.
     *
     * @param   int                 $perPage  number of results per page
     * @param   int                 $page     the page number
     * @param   Payplug             $payplug  the client configuration
     * 
     * @return  null|Resource\Payment[]   the array of payments
     *
     * @throws  Exception\InvalidPaymentException
     * @throws  Exception\UnexpectedAPIResponseException
     */
    public static function listPayments($perPage = null, $page = null, Payplug $payplug = null)
    {
    	return Resource\Payment::listPayments($perPage, $page, $payplug);
    }    
};