PayPlug PHP library documentation
======================================================

.. contents:: Table of contents

Overview
--------

The PayPlug PHP library enables developers to install the PayPlug online payment system to any website or e-commerce store, or to build a payment plugin for an existing CMS (Magento, Wordpress, WooCommerce, Prestashop, etc.).

**Payment process**

1. Your website generates a dynamic payment URL by passing the payment parameters (e.g., amount, customer ID, return URL) into the PHP library
2. Redirect your customer to that URL, which points to a secure online payment page (see an example on https://www.payplug.fr/exemple)
3. Once the payment goes through, PayPlug redirects your customer towards the return URL you specified
4. Simultaneously, PayPlug sends an Instant Payment Notification (IPN) to your server, including all transaction data, to confirm that the payment was processed.

**Security & encryption**

The PHP library enables secure data transmission via public/private RSA key encryption obtained with the OpenSSL library. In addition, credit card data is encrypted and processed exclusively on PCI DSS servers operated by Payline except ATOS. This protocol guarantees a SSL-level security without requiring to setup an SSL certificate on the merchant's website.

Installation
------------

**Clone the git repository:**
::

    git clone https://bitbucket.org/payplug/payplug_php

**Or download the library:**

Download `payplug_php`__ and unpack the tarball in your environment.

__ https://bitbucket.org/payplug/payplug_php/get/master.tar.gz

**Configuration**

PayPlug generates a set of unique parameters and keys for each user account, which needs to be saved on your server by following these configuration instructions.

Create a file called ``setup.php`` and insert the following lines to set-up the PayPlug library. Make sure to replace ``merchant@example.org`` and ``password`` with your PayPlug login information, and to replace ``PATH_TO_PAYPLUG`` with the correct path for your environment.

.. code-block:: php
   :linenos:

   <?php
   require_once("PATH_TO_PAYPLUG/payplug_php/lib/Payplug.php");
   $parameters = Payplug::loadParameters("merchant@example.org", "password");
   $parameters->saveInFile("PATH_TO_PAYPLUG/parameters.json");


You need to execute this code at least once, that is, open your web browser and go to ``http://example.org/setup.php`` (the URL where you saved the above code). Verify that everythings went well by looking at the file ``parameters.json``.

If you encounter the error ``Warning: file_put_contents(./parameters.json): failed to open stream: Permission denied in PATH_TO_PAYPLUG/lib/payplug/Parameters.php on line 53``, it is likely that you have a permission issue. Open a terminal and try ``chmod +777 .`` (note the trailing dot, it is important).

.. _create_a_payment:

Creating a payment
------------------

Create a file called ``payment.php`` that will generate a payment URL and direct the customer to the online payment page.

.. code-block:: php
   :linenos:

   <?php
   require_once("PATH_TO_PAYPLUG/payplug_php/lib/Payplug.php");
   Payplug::setConfigFromFile("PATH_TO_PAYPLUG/parameters.json");

   $paymentUrl = PaymentUrl::generateUrl(array(
                                         'amount' => 999,
                                         'currency' => 'EUR',
                                         'ipnUrl' => 'http://www.example.org/ipn.php',
                                         'email' => 'john.doe@example.fr', /* Your customer mail address */
                                         'firstName' => 'John',
                                         'lastName' => 'Doe'
                                         ));
   header("Location: $paymentUrl");
   exit();

The fields ``amount``, ``currency`` and ``ipnUrl`` are required. Note that if any of the fields ``email``, ``firstName`` or ``lastName`` is left blank, the customer will be required to enter **all three fields** on the payment page.

The complete list of accepted fields is available in the reference_ section.

Instant Payment Notification (IPN)
----------------------------------

After every successful payment or refund, PayPlug sends an Instant Payment Notification (IPN) as an HTTP POST request to the URL you provided in the ``ipnUrl`` field.

Create a file called ``ipn.php`` that will be requested after each payment. The IPN must be sent to a publicly accessible URL on your site. The following example will send a mail to the administrator each time an IPN is received

.. code-block:: php
   :linenos:

   <?php
   require_once("PATH_TO_PAYPLUG/payplug_php/lib/Payplug.php");
   Payplug::setConfigFromFile("PATH_TO_PAYPLUG/parameters.json");

   try {
       $ipn = new IPN();

       $message = "IPN received for ".$ipn->firstName." ".$ipn->lastName
                . " for an amount of ".($ipn->amount)/100." EUR";
       mail("merchant@example.org","IPN Received",$message);
   } catch (InvalidSignatureException $e) {
       mail("merchant@example.org","IPN Failed","The signature was invalid");
   }

Note that if you have not received the IPN when your client is directed to the confirmation page ``returnUrl``, we advise you to consider that the order is not confirmed to prevent the user to pay again. You should receive the IPN within a few minutes.

Finally, we recommend you create an ``IPN`` object to store all notifications received. This will help you retrieve the information in the future.

The complete list of fields sent in the IPN is available in the reference_ section.

Reference
---------

**Payment fields**

Fields marked with an * are required.

============== ======= =
Name           Type    Description
============== ======= =
amount *       Integer Transaction amount, in cents (such as ``4207`` for 42,07€). We advise you to verify that the amount is between the minimum and maximum amounts allowed for your account.
-------------- ------- -
currency *     String  Transaction currency. Only ``EUR`` is allowed at the moment.
-------------- ------- -
ipnUrl *       String  URL pointing to the ``ipn.php`` page, to which PayPlug will send payment and refund notifications. This URL must be accessible from anywhere on the Internet (usually not the case in ``localhost`` environments).
-------------- ------- -
cancelUrl      String  URL pointing to your payment cancelation page, to which PayPlug will redirect your customer if he cancels the payment.
-------------- ------- -
returnUrl      String  URL pointing to your payment confirmation page, to which PayPlug will redirect your customer after the payment.
-------------- ------- -
email          String  The customer's email address.
-------------- ------- -
firstName      String  The customer's first name.
-------------- ------- -
lastName       String  The customer's last name.
-------------- ------- -
customer       String  The customer ID in your database.
-------------- ------- -
order          String  The order ID in your database.
-------------- ------- -
customData     String  Additional data that you want to receive in the IPN.
-------------- ------- -
origin         String  Information about your website version (e.g., 'My Website 1.2') for monitoring and troubleshooting.
============== ======= =


**IPN fields**

============== ======= =
Name           Type    Description
============== ======= =
state          String  The new state of the transaction: ``paid`` or ``refunded``.
-------------- ------- -
idTransaction  Integer The PayPlug transaction ID. We recommend you save it and associate it with this order in your database.
-------------- ------- -
amount         Integer Transaction amount, in cents (such as ``4207`` for 42,07€).
-------------- ------- -
email          String  The customer's email address, either provided when creating the payment URL or entered manually on the payment page by the customer.
-------------- ------- -
firstName      String  The customer's first name, either provided when creating the payment URL or entered manually on the payment page by the customer.
-------------- ------- -
lastName       String  The customer's last name, either provided when creating the payment URL or entered manually on the payment page by the customer.
-------------- ------- -
customer       String  Customer ID provided when creating the payment URL.
-------------- ------- -
order          String  Order ID provided when creating the payment URL.
-------------- ------- -
customData     String  Custom data provided when creating the payment URL.
-------------- ------- -
origin         String  Information about your website version (e.g., 'My Website 1.2 payplug_php0.9 PHP 5.3'), provided when creating the payment URL, with additional data sent by the library itself.
============== ======= =



Frequently asked questions
--------------------------

**How to test a payment?**

We do not have a sandbox environment. However, we suggest you run actual transactions and then refund them via the PayPlug portal (we will even refund transaction fees). This will allow you to test out your integration in real conditions without any charge.

**How to run unit testing on my configuration?**

In order to run tests you have to install **PHPUnit**. Then just do the following to run the test suite:
::

    phpunit PATH_TO_PAYPLUG/payplug_php/tests/


