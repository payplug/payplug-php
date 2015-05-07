<?php

/**
 * Payment Exception.
 */
abstract class PayPlug_PaymentException extends PayPlug_PayPlugException
{
}

/**
 * Customer's card is stolen.
 */
class PayPlug_CardStolenException extends PayPlug_PaymentException
{
}

/**
 * Customer's car is lost.
 */
class PayPlug_CardLostException extends PayPlug_PaymentException
{
}