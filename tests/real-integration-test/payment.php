<?php

require_once("../../lib/Payplug.php");

$parametersFile = __DIR__ . "/params.json";
$parameters;

$amount = (float) $_POST["amount"] * 100;
$email = $_POST["email"];
$firstName = $_POST["firstName"];
$lastName = $_POST["lastName"];
$ipnUrl = $_POST["ipnUrl"];

/* Loads parameters (from PayPlug if needed) */
if ( ! file_exists($parametersFile)) {
    try {
        $parameters = Payplug::loadParameters("testlib@payplug.fr", "123456789");
        $parameters->saveInFile($parametersFile);
    } catch (Exception $e) {
        die("Fail : \n" . $e->getMessage());
    }
}
else {
    try {
        $parameters = Parameters::loadFromFile($parametersFile);
    } catch (Exception $e) {
        die("Fail : \n" . $e->getMessage());
    }
}

Payplug::setConfig($parameters);

/* Creates a payment request */
$paymentUrl;
$payment = new PaymentUrl($amount, "EUR", $ipnUrl);

$payment->customData = "29";
$payment->customer = "2";
$payment->email = $email;
$payment->firstName = $firstName;
$payment->lastName = $lastName;
$payment->order = "42";

try {
    $paymentUrl = $payment->generateUrl();

    header("Location: $paymentUrl");
    exit();
} catch (Exception $e) {
    die("Fail : \n" . $e->getMessage());
}

