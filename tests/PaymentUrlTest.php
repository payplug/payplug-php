<?php

require_once(__DIR__ . "/../lib/Payplug.php");

class PaymentUrlTest extends PHPUnit_Framework_TestCase {

    protected static $data;

    public static function setUpBeforeClass() {
        /* Override this variable so unit tests don't fail on different php version */
        PaymentUrl::$phpVersion = "unit-42"; 

        self::$data = array(
            'amount' => 4200,
            'currency' => "EUR",
            'ipnUrl' => "http://www.monsite.com/ipn",
        );
    }

    public static function tearDownAfterClass() 
    {
    }

    /**
     * @expectedException ParametersNotSetException
     */
    public function testGenerateUrlParametersNotSetException() {
        PaymentUrl::generateUrl(self::$data);
    }

    public function testPaymentUrl() {
        Payplug::setConfigFromFile(__DIR__."/PaymentUrlTest_parameters.json");
        $paymentUrl = PaymentUrl::generateUrl(self::$data);
        $this->assertNotNull($paymentUrl);
    }

    /**
     * @expectedException MalformedURLException
     */
    public function testGenerateUrlMalformedIpnUrl() {
        $malformed = self::$data;
        $malformed['ipnUrl'] = "www.monsite.com/ipn";

        PaymentUrl::generateUrl($malformed);
    }

    /**
     * @expectedException MalformedURLException
     */
    public function testGenerateUrlMalformedReturnUrl() {
        $malformed = self::$data;
        $malformed['ipnUrl'] = "https://www.monsite.com/ipn";
        $malformed['returnUrl'] = "www.monsite.com/thankyou";

        PaymentUrl::generateUrl($malformed);
    }

    public function testGenerateUrlWithoutCustomerInfos() {
        self::$data['ipnUrl'] = "https://www.monsite.com/ipn";
        self::$data['returnUrl'] = "http://www.monsite.com/thankyou";

        $expectedUrl = "https://www.payplug.fr/p/MD1iqp-NEeO1vBIxQwfJEg==?data=YW1vdW50PTQyMDAmY3VycmVuY3k9RVVSJmlwbl91cmw9aHR0cHMlM0ElMkYlMkZ3d3cubW9uc2l0ZS5jb20lMkZpcG4mb3JpZ2luPStwYXlwbHVnLXBocCswLjkrUEhQK3VuaXQtNDImcmV0dXJuX3VybD1odHRwJTNBJTJGJTJGd3d3Lm1vbnNpdGUuY29tJTJGdGhhbmt5b3U%3D&sign=df8Jsj3EfemN9XQxne16%2Bc2k2udDPLn7KFmqxwejhxNRgJRYX5H9jNExcGJVmAzpzs3gIY8tteH9V8pfmKH3R71XHYTBn7fKEa3llbo%2BrdhFztOf3ptiFgQxVAqyMdbQGPbS68f5XkHvY734o6UgFUbmM7t3VA4P1NNpLbVXp4XfxEEk6NgmC4BCJy5DfIdBWKsJHCuRF82BpvwYGpjcjVgT3TbbaZGt9riSDjoJtQxmNd4A%2FapVP2A995PqfpaIlu%2FoAlawGLsegau8FlaEPhbpSqniAao1kHB7FiYtoB3gUqGTFa2wwGJl7fS4s%2Fq4AmnAbVVGzXVMlPDAszbrjg%3D%3D";
        $generatedUrl = PaymentUrl::generateUrl(self::$data);

        $this->assertEquals($expectedUrl, $generatedUrl);
    }

    public function testGenerateUrlWithCustomerInfos() {
        self::$data['email'] = "testlib@payplug.fr";
        self::$data['firstName'] = "John";
        self::$data['lastName'] = "Doe";

        $expectedUrl = "https://www.payplug.fr/p/MD1iqp-NEeO1vBIxQwfJEg==?data=YW1vdW50PTQyMDAmY3VycmVuY3k9RVVSJmVtYWlsPXRlc3RsaWIlNDBwYXlwbHVnLmZyJmZpcnN0X25hbWU9Sm9obiZpcG5fdXJsPWh0dHBzJTNBJTJGJTJGd3d3Lm1vbnNpdGUuY29tJTJGaXBuJmxhc3RfbmFtZT1Eb2Umb3JpZ2luPStwYXlwbHVnLXBocCswLjkrUEhQK3VuaXQtNDImcmV0dXJuX3VybD1odHRwJTNBJTJGJTJGd3d3Lm1vbnNpdGUuY29tJTJGdGhhbmt5b3U%3D&sign=aB98TWz8do17vQilm0E4wU%2FyEHnLdAZd3UV5X9LeVPaDf9qIeumrvwsHizud9hpOIwMR8aByhgNCECvCX3gb3aOBumZJjFYrW2rITaygLComoXbcUKdoWVEBpExcZK4bjzVyX9hW0%2FF8hmxmevjHsRaLsbsTdDWmEDsVksSi7CtmAT6rVYPH6QnPQ4LVnNXTKDcImHshxvuyMLmAx4tCGV51GkWSV%2FBKZdv8%2Bc1smecwhFvEmFU%2BVBvyt170O7nmLfYsHA3j4LfAejU7l4M7WymYBoGPAbUfTJJ1pcMMWOoejK860%2BPxs2XXZ5FE08Qb%2BYvTxYp0NXpo5oIreG%2BVoQ%3D%3D";
        $generatedUrl = PaymentUrl::generateUrl(self::$data);

        $this->assertEquals($expectedUrl, $generatedUrl);
    }
}

