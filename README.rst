PHP library for the Payplug API
===============================

.. image:: https://travis-ci.org/payplug/payplug-php.svg?branch=master
   :target: https://travis-ci.org/payplug/payplug-php
   :alt: CI Status

.. image:: https://img.shields.io/packagist/v/payplug/payplug-php.svg
   :target: https://packagist.org/packages/payplug/payplug-php
   :alt: Packagist

This is the documentation of Payplug's PHP library. It is designed to
help developers use Payplug as a payment solution in a simple, yet robust way.

You can create a Payplug account at https://www.payplug.com/.

Maintenance
-----------

CA certificate (cacert.pem) should be updated every year during the first week of December.
Go to https://curl.se/docs/caextract.html to get a recent one.

Prerequisites
-------------

Payplug's library relies on **cURL** to perform HTTP requests and requires **OpenSSL (1.0.1 or newer)** to secure transactions. You also need **PHP 5.3** or newer for the Payplug PHP V2.

For version **PHP 5.2** or older, you must refer to Payplug PHP V1.

Documentation
-------------

Please see https://docs.payplug.com/api for the latest documentation.

Installation
-------------

**Option 1 - Strongly preferred)** via composer:

- Get composer from the `composer website`_.
- Make sure you have initialized your *composer.json*.
- Run *composer require payplug/payplug-php* from your project directory.

.. _composer website: https://getcomposer.org/download/

**Option 2-** clone the repository:

::

    git clone https://github.com/payplug/payplug-php.git

**Option 3)** download as a tarball:

- Download the most recent tarball from the `download page V2`_ (V2 for **PHP 5.3** or newer)
- Download the most recent tarball from the `download page V1`_ (V1 for **PHP 5.2** or older)
- Unpack the tarball
- Put the files somewhere in your project

.. _download page V1: https://github.com/payplug/payplug-php/releases/tag/V1.1.2
.. _download page V2: https://github.com/payplug/payplug-php/releases

__ https://bitbucket.org/payplug/payplug_php/downloads#tag-downloads

To get started, add the following to your PHP script (if you are not running a framework):

.. code-block:: php

    <?php
    require_once("PATH_TO_PAYPLUG/payplug_php/lib/init.php");

Project Git Workflow
=====================

This repository follows a specific Git workflow to ensure smooth collaboration and controlled releases. Please follow the steps outlined below when contributing to this project.

Git Workflow Steps
------------------

1. **Create a feature or fix branch:**

   Before making any changes, create a new branch from the `develop` branch:

   ::

       git checkout develop
       git pull origin develop
       git checkout -b <feature-name>

   For a bug fix, use:

   ::

       git checkout develop
       git pull origin develop
       git checkout -b <fix-name>

2. **Work on your feature or fix:**

   Make your code changes and commit them to your feature or fix branch.

3. **Create a merge request:**

   Once your feature or fix is ready, create a merge request from your branch to the `develop` branch. Get your changes reviewed by your peers.

4. **Release preparation:**

   When it's time for a release, create an intermediary branch called `release-<version-number>` from the `develop` branch:

   ::

       git checkout develop
       git pull origin develop
       git checkout -b release-<version-number>

5. **Finalize the release:**

   Test the code on the `release-<version-number>` branch thoroughly. Fix any bugs or issues that arise.

6. **Merge to master:**

   Once the release is tested and stable, create a merge request from the `release-<version-number>` branch to the `master` branch. This signifies a successful release.

7. **Tag the release:**

   After the merge to `master`, create a new tag to mark the release version:

   ::

       git checkout master
       git pull origin master
       git tag -a v<version-number> -m "Release <version-number>"
       git push origin master --tags

Usage
-----

Here's how simple it is to create a payment request:

.. code-block:: php

    <?php
    require_once("PATH_TO_PAYPLUG/payplug_php/lib/init.php"); // If not using a framework

    // Loads your account's parameters that you've previously downloaded and saved
    Payplug\Payplug::init(array(
      'secretKey' => 'sk_live_YOUR_PRIVATE_KEY',
      'apiVersion' => 'THE_API_VERSION_YOU_WANT',
    ));

    // Create a payment request of €9.99. The payment confirmation (IPN) will be sent to "'https://example.net/notifications?id='.$customer_id".
    // Note that all amounts must be expressed in centimes as positive whole numbers (€9.99 = 999 centimes).
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

    // You will be able to find how the payment object is built in the documentation.
    // For instance, if you want to get a URL to the payment page, you can do:
    $paymentUrl = $payment->hosted_payment->payment_url;

    // Then, you can redirect the user to the payment page
    header("Location: $paymentUrl");

Go further:
-----------
Tests:
++++++
See tests/README.rst.

Project Owners
--------------

This project is maintained by:


- [Imène Lajili](https://github.com/ilajili)
- [Manuel Mesquita](https://github.com/PPmmesquita)

For any questions or concerns about the workflow, feel free to reach out to the project owners.


