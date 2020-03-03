PHP library for the PayPlug API
===============================

.. image:: https://travis-ci.org/payplug/payplug-php.svg?branch=master
   :target: https://travis-ci.org/payplug/payplug-php
   :alt: CI Status

.. image:: https://img.shields.io/packagist/v/payplug/payplug-php.svg
   :target: https://packagist.org/packages/payplug/payplug-php
   :alt: Packagist

This is the documentation of PayPlug's PHP library. It is designed to
help developers to use PayPlug as payment solution in a simple, yet robust way.

You can create a PayPlug account at https://www.payplug.com/.

Prerequisites
-------------

PayPlug's library relies on **cURL** to perform HTTP requests and requires **OpenSSL (1.0.1 or newer)** to secure transactions. You also need **PHP 5.3** or newer for the PayPlug PHP V2.

For version **PHP 5.2** or older you must refer to PayPlug PHP V1.

Documentation
-------------
Please see https://docs.payplug.com/api for latest documentation.

Installation
------------
**Option 1 - Strongly preferred)** via composer:

- Get composer from `composer website`_.
- Make sure you have initialized your *composer.json*.
- Run *composer require payplug/payplug-php* from your project directory.

.. _composer website: https://getcomposer.org/download/

**Option 2)** clone the repository :
::

    git clone https://github.com/payplug/payplug-php.git

**Option 3)** download as a tarball :

- Download the most recent tarball from the `download page V2`_ (V2 for **PHP 5.3** or newer)
- Download the most recent tarball from the `download page V1`_ (V1 for **PHP 5.2** or older)
- Unpack the tarball
- Put the files somewhere in your project

.. _download page V1: https://github.com/payplug/payplug-php/releases/tag/V1.1.2
.. _download page V2: https://github.com/payplug/payplug-php/releases

__ https://bitbucket.org/payplug/payplug_php/downloads#tag-downloads

To get started, add the following to your PHP script (if you are not running a framework):

.. sourcecode :: php

    <?php
    require_once("PATH_TO_PAYPLUG/payplug_php/lib/init.php");

Usage
-----

Here's how simple it is to create a payment request:

.. sourcecode :: php

    <?php
    require_once("PATH_TO_PAYPLUG/payplug_php/lib/init.php"); // If not using a framework

    // Loads your account's parameters that you've previously downloaded and saved
    Payplug\Payplug::init(array(
      'secretKey' => 'sk_live_YOUR_PRIVATE_KEY',
      'apiVersion' => 'THE_API_VERSION_YOU_WANT',
    ));

    // Create a payment request of €9.99. The payment confirmation (IPN) will be sent to "'https://example.net/notifications?id='.$customer_id".
    // note that all amounts must be expressed in centimes as positive whole numbers (€9.99 = 999 centimes).
    // Metadata allow you to include additional information when processing payments or refunds.
    $customer_id = '42710';

    $payment = Payplug\Payment::create(array(
            'amount'            => 999,
            'currency'          => 'EUR',
            'billing'          => array(
                'title'        => 'mr',
                'first_name'   => 'John',
                'last_name'    => 'Watson',
                'email'        => 'john.watson@example.net',
                'address1'     => '221B Baker Street',
                'postcode'     => 'NW16XE',
                'city'         => 'London',
                'country'      => 'GB',
                'language'     => 'en'
            ),
            'shipping'          => array(
                'title'         => 'mr',
                'first_name'    => 'John',
                'last_name'     => 'Watson',
                'email'         => 'john.watson@example.net',
                'address1'      => '221B Baker Street',
                'postcode'      => 'NW16XE',
                'city'          => 'London',
                'country'       => 'GB',
                'language'      => 'en',
                'delivery_type' => 'BILLING'
            ),
            'hosted_payment' => array(
                'return_url' => 'https://example.net/return?id='.$customer_id,
                'cancel_url' => 'https://example.net/cancel?id='.$customer_id
            ),
            'notification_url' => 'https://example.net/notifications?id='.$customer_id,
            'metadata'         => array(
                'customer_id'  => $customer_id
            )
    ));
    ?>

    // You will be able to find how the payment object is built in the documentation.
    // For instance, if you want to get an URL to the payment page, you get do:
    $paymentUrl = $payment->hosted_payment->payment_url;

    // Then, you can redirect the user to the payment page
    header("Location: $paymentUrl");

Go further:
-----------
Tests:
++++++
See tests/README.rst.
