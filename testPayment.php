<?php
require_once("lib/PayPlug.php");

// Loads your account's parameters that you've previously downloaded and saved
PayPlug_ClientConfiguration::initialize(array(
    'LIVE_TOKEN'        => 'sk_live_512fd6873f246339517d00180f7e5dfe',
    'TEST_TOKEN'        => 'sk_test_b3baa16442266f941427dac365a4dd72',
    'TEST_MODE_ENABLED' => true // Or false if you want to perform real transactions
    )
);

// Create a payment request of €9.99. The payment confirmation (IPN) will be sent to "http://www.example.org/callbackURL"
$payment = PayPlug_Payment::create(array(
    'amount'            => 999,
    'customer'          => array(
        'email'         => 'john.doe@example.com',
        'first_name'    => 'John',
        'last_name'     => 'Doe'
    ),
    'hosted_payment'    => array(
        'notification_url'  => 'http://www.example.org/callbackURL',
        'return_url'        => 'https://www.example.com/thank_you_for_your_payment.html',
        'cancel_url'        => 'https://www.example.com/so_bad_it_didnt_make_it.html'
    ),
    'force_3ds'         => false
));

// You will be able to find how the payment object is built in the documentation.
// For instance, if you want to get an URL to the payment page, you get do:
$paymentUrl = $payment->getAttribute('hosted_payment')->getAttribute('payment_url');

// Then, you can redirect the user to the payment page
header("Location: $paymentUrl");
exit();
?>