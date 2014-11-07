<?php

/**
 * Represents an IPN, which is what the merchant receives after one of
 * its customers has paid via Payplug.
 */
class IPN {

    public $amount;
    public $customData;
    public $customer;
    public $email;
    public $firstName;
    public $idTransaction;
    public $lastName;
    public $order;
    public $origin;
    public $state;
    public $isTest;

    public function __construct($headers = null, $body = null) {
        $config = Payplug::getConfig();

        /* Checks if all needed parameters have been provided */
        // If the merchant's parameters have not been set
        if (is_null($config)) {
            throw new ParametersNotSetException();
        }
        // If IPN content are not provided
        if (is_null($body)) {
            // Get content from default source
            $body = file_get_contents("php://input");
        }
        // If IPN headers are not provided
        if (is_null($headers)) {
            // Get headers from default source
            $headers = getallheaders();
        }

        /* Checks if the signature, and by extension the IPN, is valid */
        $headers = array_change_key_case($headers, CASE_UPPER);
        $signature = base64_decode($headers['PAYPLUG-SIGNATURE']);
        $publicKey = openssl_pkey_get_public($config->payplugPublicKey);

        $isValid = (openssl_verify($body, $signature, $publicKey, OPENSSL_ALGO_SHA1) === 1);

        if ( ! $isValid) {
            throw new InvalidSignatureException();
        }

        /* Extracts data from the IPN if it's valid */
        $data = json_decode($body, true);

        $this->amount = $data['amount'];
        $this->customData = $data['custom_data'];
        $this->customer = $data['customer'];
        $this->email = $data['email'];
        $this->firstName = $data['first_name'];
        $this->idTransaction = $data['id_transaction'];
        $this->lastName = $data['last_name'];
        $this->order = $data['order'];
        $this->origin = $data['origin'];
        $this->state = $data['state'];
        $this->isTest = $data['is_test'];
    }
}

