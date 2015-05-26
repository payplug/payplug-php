Welcome to PayPlug e-commerce library's documentation!
======================================================

This is the documentation of PayPlug's e-commerce PHP library. It is designed to
help developers to use PayPlug as payment solution in a simple, yet robust way.

Prerequisites
-------------

PayPlug's library relies on **cURL** to perform HTTP requests and requires **OpenSSL** to secure transactions. You also need **PHP 5.2** or newer.

Installation
------------

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
    require_once("PATH_TO_PAYPLUG/payplug_php/lib/PayPlug.php");
    
Usage
-----

Here's how simple it is to create a payment request:

.. sourcecode :: php

    <?php
    require_once("PATH_TO_PAYPLUG/payplug_php/lib/PayPlug.php");

    // Loads your account's parameters that you've previously downloaded and saved
    PayPlug_ClientConfiguration::initialize(array(
        'LIVE_TOKEN'        => 'YOUR_LIVE_TOKEN',
        'TEST_TOKEN'        => 'YOUR_TEST_TOKEN',
        'TEST_MODE_ENABLED' => true // Or false if you want to perform real transactions
        )
    );

    // Create a payment request of €9.99. The payment confirmation (IPN) will be sent to "http://www.example.org/callbackURL"
    $payment = PayPlug_Payment::create(array(
            'amount'            => 999,
            'currency'          => 'EUR',
            'customer'          => array(
                'email'         => 'john.doe@example.com',
                'first_name'    => 'John',
                'last_name'     => 'Doe'
            ),
            'hosted_payment'    => array(
                'notification_url'  => 'http://www.example.org/callbackURL',
                'return_url'        => 'https://www.example.com/thank_you_for_your_payment.html',
                'cancel_url'        => 'https://www.example.com/so_bad_it_didnt_make_it.html'
            )
    ));

    // You will be able to find how the payment object is built in the documentation.
    // For instance, if you want to get an URL to the payment page, you get do:
    $paymentUrl = $payment->hosted_payment->payment_url;

    // Then, you can redirect the user to the payment page
    header("Location: $paymentUrl");
    exit();

Go further:
-----------
Documentation:
++++++++++++++
You can generate the PHPDocumentation using PhpDocumentor. Download it and add it to your PATH.
Run phpdoc inside lib/ directory and set the output to docs/ directory.

    phpdoc -d lib/ -t docs/

Tests:
++++++
See tests/README.rst.