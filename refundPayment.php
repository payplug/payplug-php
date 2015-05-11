<?php
require_once 'lib/PayPlug.php';

PayPlug_ClientConfiguration::initialize(array(
    'LIVE_TOKEN'        => 'sk_live_512fd6873f246339517d00180f7e5dfe',
    'TEST_TOKEN'        => 'sk_test_b3baa16442266f941427dac365a4dd72',
    'TEST_MODE_ENABLED' => true // Or false if you want to perform real transactions
    )
);

$refund1 = PayPlug_Refund::create('pay_5iGE9e9mTMQLTMwsMbAQnu', array('amount' => 100));
$refund2 = PayPlug_Refund::create('pay_5iGE9e9mTMQLTMwsMbAQnu', array('amount' => 100));
$refunds = PayPlug_Refund::list_refunds('pay_5iGE9e9mTMQLTMwsMbAQnu');
$refunds = PayPlug_Refund::list_refunds(PayPlug_Payment::retrieve('pay_5iGE9e9mTMQLTMwsMbAQnu'));
var_dump($refunds);
