<?php
require_once 'lib/PayPlug.php';

PayPlug_ClientConfiguration::initialize(array(
    'LIVE_TOKEN'        => 'sk_live_512fd6873f246339517d00180f7e5dfe',
    'TEST_TOKEN'        => 'sk_test_b3baa16442266f941427dac365a4dd72',
    'TEST_MODE_ENABLED' => true // Or false if you want to perform real transactions
    )
);

$refund = PayPlug_Refund::list_refunds('pay_2VxSaWaYHZXSYvskPPgK5o');
var_dump($refund);