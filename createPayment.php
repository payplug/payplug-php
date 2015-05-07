<?php
require_once 'lib/PayPlug.php';

PayPlug_ClientConfiguration::initialize(array(
    'LIVE_TOKEN'        => 'sk_live_512fd6873f246339517d00180f7e5dfe',
    'TEST_TOKEN'        => 'sk_test_b3baa16442266f941427dac365a4dd72',
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
        ),
    'force_3ds'         => false
));

echo $payment->getAttribute('id') . "\n" . $payment->getAttribute('hosted_payment')->getAttribute('payment_url');
