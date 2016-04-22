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
  letters in **P**ay**P**lug). If you use ***P**ayplugException* and ***P**ayplugServerException* classes (with one
  uppercase letter in Payplug), you have nothing to do.
- **NEW**: This library is now under MIT Licence (Issue #4).