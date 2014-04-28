<?php

/**
 * Represents the set of parameters that a merchant need to perform payments.
 */
class Parameters {

    /**
     * Creates a `Parameters` object from the provided string.
     */
    public static function createFromString($str) {
        $array = json_decode($str, true);

        return new Parameters(
            $array["currencies"],
            $array["maxAmount"],
            $array["minAmount"],
            $array["paymentBaseUrl"],
            $array["payplugPublicKey"],
            $array["privateKey"]
        );
    }

    /**
     * Reads the content of the file located at `path` and then attempt
     * to create a `Parameters` object from it.
     */
    public static function loadFromFile($path) {
        return self::createFromString(file_get_contents($path));
    }

    public $currencies;
    public $maxAmount;
    public $minAmount;
    public $paymentBaseUrl;
    public $payplugPublicKey;
    public $privateKey;

    public function __construct($currencies, $maxAmount, $minAmount, $paymentBaseUrl, $payplugPublicKey, $privateKey) {
        $this->currencies = $currencies;
        $this->maxAmount = $maxAmount;
        $this->minAmount = $minAmount;
        $this->paymentBaseUrl = $paymentBaseUrl;
        $this->payplugPublicKey = $payplugPublicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * Saves the parameters in the file located at `path`
     * by overriding its content.
     */
    public function saveInFile($path) {
        file_put_contents($path, $this->toString());
    }

    /**
     * Returns a string representation of this object. This method is
     * used when saving the parameters in a file.
     */
    public function toString() {
        return json_encode($this);
    }
}
