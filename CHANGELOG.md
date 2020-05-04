3.3.0
-----
- NEW: Add AccountingReport class to handle new API endpoint

3.2.0
-----

- **NEW**: Add Oney Payment Simulations

3.1.0
-----

- **NEW**: Deprecated Payplug::setSecretKey function, use Payplug::init instead
- **NEW**: API version should now be specified explicitly from module
- **NEW**: Improve Payplug setSecretKey with init method
- **NEW**: Excluded configuration API_VERSION versionning, it will be send from the apps nowadays

3.0.0
-----

- **Major**: Replace customer by shipping and billing to be compliant with PSD2.
- **NEW**: Add versionning with new API_VERSION parameter.


2.7.0
-----

- **NEW**: Add deferred payments.


2.6.0
-----

- **NEW**: Add installment plans.


2.5.1
-----

- **Bugfix**: Fix packagist bundle.


2.5.0
-----

- **NEW**: Remove classes and methods specific to customer
- **NEW**: Add Authentication class for method specific to login and account permissions
- **NEW**: Add a method to patch payment

2.4.0
-----

- **NEW**: Support for __isset magic method in resources. This should bring Twig support.
  (Thanks [oservieres](https://github.com/oservieres))
- **NEW**: Add a method to specify custom User-Agent products.

2.3.0
-----

- **NEW**: Support for Customer/Cards. (see official documentation)
- **NEW**: Payment objects can now be aborted.

  ```
  $payment->abort();
  ```

- **Breaking change**: Drop *data* key in objects collections (e.g. list of payments/list of refunds):

  ```
  $payments = \Payplug\Payment::listPayments();
  // $payment = $payments['data'][0]; // BEFORE
  $payment = $payments[0]; // NEW BEHAVIOR

  $refunds = \Payplug\Refund::listRefunds($payment);
  // $refund = $refunds['data'][0]; // BEFORE
  $refund = $refunds[0]; // NEW BEHAVIOR
  ```

- **Breaking change**: Drop deprecated classes *PayPlugException* and *PayPlugServerException* (with two uppercase
  letters in **P**ay**P**lug). If you use **P**ay**p**lugException* and **P**ay**p**lugServerException* classes (with one
  uppercase letter in Payplug), you have nothing to do.
- **NEW**: This library is now under MIT Licence (Issue #4).
