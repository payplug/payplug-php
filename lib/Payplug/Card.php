<?php
namespace Payplug;

/**
 * The Card DAO simplifies the access to most useful methods
 **/
class Card
{
    /**
     * Delete a card.
     *
     * @param   string|\Payplug\Resource\Card $card the card ID or object
     * @param   Payplug $payplug the client configuration
     *
     * @return  null|Resource\Card the deleted card or null on error
     *
     * @throws  Exception\ConfigurationNotSetException
     */
    public static function delete($card, Payplug $payplug = null)
    {
        return Resource\Card::deleteCard($card, $payplug);
    }
};