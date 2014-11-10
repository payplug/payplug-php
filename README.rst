Welcome to PayPlug e-commerce library's documentation!
======================================================

This is the documentation of PayPlug's e-commerce library. It is designed to
help developers to use PayPlug as payment solution in a simple, yet robust way.

Prerequisites
=============

PayPlug's library relies on **cURL** to perform HTTP requests and requires **OpenSSL** to secure transactions. You also need **PHP 5.2** or newer.

Installation
============

**Option 1)** clone the repository :
::

    git clone https://bitbucket.org/payplug/payplug_php

**Option 2)** download as a tarball :

- Download the most recent tarball from the `download page`__
- Unpack the tarball
- Put the files somewhere in your project

__ https://bitbucket.org/payplug/payplug_php/downloads#tag-downloads

To get started, add the following to your PHP script :

.. sourcecode :: php

    <?php
    require_once("PATH_TO_PAYPLUG/payplug_php/lib/Payplug.php");
    
Usage
=====

Here's how simple it is to create a payment request :

.. sourcecode :: php

    <?php
    require_once("PATH_TO_PAYPLUG/payplug_php/lib/Payplug.php");

    // Loads your account's parameters that you've previously downloaded and saved
    Payplug::setConfigFromFile("PATH_TO_PAYPLUG/parameters.json");

    // Creating a payment request of €9.99. The payment confirmation (IPN) will be sent to "http://www.example.org/callbackURL"
    $paymentUrl = PaymentUrl::generateUrl(array(
                                         'amount' => 999,
                                         'currency' => 'EUR',
                                         'ipnUrl' => 'http://www.example.org/ipn.php',
                                         'email' => 'john.doe@example.fr', /* Your customer mail address */
                                         'firstName' => 'John',
                                         'lastName' => 'Doe'
                                         ));

    // Redirects the user to the payment page
    header("Location: $paymentUrl");
    exit();

