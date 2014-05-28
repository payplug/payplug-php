<?php

/**
 * This class is used to generate a payment URL.
 */
class PaymentUrl {

    public $amount;
    public $currency;
    public $customData;
    public $customer;
    public $email;
    public $firstName;
    public $ipnUrl;
    public $lastName;
    public $order;
    public $origin;
    public $returnUrl;

    /**
     * The method which actually generates the URL.
     */
    public static function generateUrl($params) {
        $config = Payplug::getConfig();
        $data;
        $signature;

        // If the merchant's parameters have not been set
        if (! $config) {
            // Something bad will happen
            throw new ParametersNotSetException();
        }
        if (! isset($params['amount'])) {
            throw new MissingRequiredParameterException("Missing required parameter: amount");
        }
        if (! isset($params['currency'])) {
            throw new MissingRequiredParameterException("Missing required parameter: currency");
        }
        if (! isset($params['ipnUrl'])) {
            throw new MissingRequiredParameterException("Missing required parameter: ipnUrl");
        }
        if (! preg_match("/^(http|https):\/\//i", $params['ipnUrl'])) {
            throw new MalformedURLException($params['ipnUrl'] . " doesn't starts with 'http://' or 'https://'");
        }
        if ( isset($params['returnUrl']) && ! preg_match("/^(http|https):\/\//i", $params['returnUrl'])) {
            throw new MalformedURLException($params['returnUrl'] . " doesn't starts with 'http://' or 'https://'");
        }

        /* Generation of the <data> parameter */
        $remap_params=array(
            /* our key => payplug key */
            "amount" => 'amount',
            "currency" => 'currency',
            "customData" => 'custom_data',
            "customer" => 'customer',
            "email" => 'email',
            "firstName" => 'first_name',
            "ipnUrl" => 'ipn_url',
            "lastName" => 'last_name',
            "order" => 'order',
            "origin" => 'origin',
            "returnUrl" => 'return_url',
        );
        $payment_params=array();

        /* Remaps $params keys to the one expected by Payplug payment page
         * That is, transform array('amount'=>100,'firstName'=>'bob')
         * to                 array('amount'=>100,'first_name'=>'bob')
         */
        foreach ($remap_params as $our_key => $payplug_key){
            if (isset($params[$our_key]))
                $payment_params[$payplug_key] = $params[$our_key];
            if ($our_key == 'origin')
                $payment_params[$payplug_key] = (isset($params[$our_key]) ? $params[$our_key] : "")." payplug-php ".Payplug::VERSION." PHP ".phpversion();
        }

        $url_params = http_build_query($payment_params);
        $data = urlencode(base64_encode($url_params));

        /* Generation of the <signature> parameter */
        $privateKey = openssl_pkey_get_private($config->privateKey);
        openssl_sign($url_params, $signature, $privateKey, OPENSSL_ALGO_SHA1);
        $signature = urlencode(base64_encode($signature));

        return $config->paymentBaseUrl . "?data=" . $data . "&sign=" . $signature;
    }
}
