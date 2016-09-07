<?php
namespace Payplug;

/**
 * The Customer DAO simplifies the access to most useful methods
 **/
class Customer
{
     /**
     * Retrieve a customer.
     *
     * @param   string $customerId the payment ID
     * @param   Payplug $payplug the client configuration
     *
     * @return  null|Resource\Customer the retrieved payment or null on error
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function retrieve($customerId, Payplug $payplug = null)
    {
    	return Resource\Customer::retrieve($customerId, $payplug);
    }

     /**
     * Update a customer.
     *
     * @param   string $customerId the customer ID
     * @param   array $data the data to update
     * @param   Payplug $payplug  the client configuration
     *
     * @return  Resource\Customer the updated customer
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function update($customerId, $data, Payplug $payplug = null)
    {
        $customer = Resource\Customer::fromAttributes(array('id' => $customerId));
    	return $customer->update($data, $payplug);
    }

	/**
     * Delete a customer.
     *
     * @param   string $customerId the customer ID
     * @param   Payplug $payplug  the client configuration
     *
     * @return  Resource\Customer the updated customer
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function delete($customerId, Payplug $payplug = null)
    {
        $customer = Resource\Customer::fromAttributes(array('id' => $customerId));
    	return $customer->delete($payplug);
    }

    /**
     * Create a customer.
     *
     * @param   array $data API data for customer creation
     * @param   Payplug $payplug the client configuration
     *
     * @return  Resource\Customer the created payment instance
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function create(array $data, Payplug $payplug = null)
    {
    	return Resource\Customer::create($data, $payplug);
    }

    /**
     * List customers.
     *
     * @param   int     $perPage  number of results per page
     * @param   int     $page     the page number
     * @param   Payplug $payplug  the client configuration
     * 
     * @return  Resource\Customer[] the array of customers
     *
     * @throws  Exception\InvalidPaymentException
     * @throws  Exception\UnexpectedAPIResponseException
     */
    public static function listCustomers($perPage = null, $page = null, Payplug $payplug = null)
    {
    	return Resource\Customer::listCustomers($perPage, $page, $payplug);
    }
};