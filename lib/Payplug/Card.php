<?php
namespace Payplug;

/**
 * The Card DAO simplifies the access to most useful methods
 **/
class Card
{
	/**
     * Retrieve a card.
     *
     * @param   string|\Payplug\Resource\Customer $customer The customer ID or object
     * @param   string $cardId The card ID
     * @param   Payplug $payplug  the client configuration
     *
     * @return  Resource\Card the retrieved card
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function retrieve($customer, $cardId, Payplug $payplug = null)
    {
    	return Resource\Card::retrieve($customer, $cardId, $payplug);
    }


    /**
     * Create a card.
     *
     * @param   string|\Payplug\Resource\Customer $customer The customer ID or object
     * @param   array $data API data for payment creation
     * @param   Payplug $payplug  the client configuration
     *
     * @return  Resource\Card the created card instance
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function create($customer, array $data, Payplug $payplug = null)
    {
    	return Resource\Card::create($customer, $data, $payplug);
    }

    /**
     * Delete a card.
     *
     * @param   string|\Payplug\Resource\Customer $customer The customer ID or object
     * @param   string|\Payplug\Resource\Card $card the card ID or object
     * @param   Payplug $payplug the client configuration
     *
     * @return  Resource\Card the updated customer
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function delete($customer, $card, Payplug $payplug = null)
    {
        Resource\Card::deleteCard($customer, $card, $payplug);
    }

    /**
     * List the cards of a customer.
     *
     * @param   string|Customer $customer the customer id or the customer object
     * @param   Payplug $payplug the client configuration
     *
     * @return  Card[] an array containing the refunds on success.
     *
     * @throws Exception\ConfigurationNotSetException
     * @throws Exception\UnexpectedAPIResponseException
     */
    public static function listCards($customer, Payplug $payplug = null)
    {
        return Resource\Card::listCards($customer, $payplug);
    }
};