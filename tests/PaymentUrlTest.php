<?php

require_once(__DIR__ . "/../lib/Payplug.php");

class PaymentUrlTest extends PHPUnit_Framework_TestCase {

    protected static $paymentUrl;

    public static function setUpBeforeClass() {
        self::$paymentUrl = new PaymentUrl(4200, "EUR", "http://www.monsite.com/ipn");
    }

    public static function tearDownAfterClass() 
    {
    }

    public function testPaymentUrl() {
        $this->assertTrue(self::$paymentUrl->amount == 4200);
        $this->assertTrue(self::$paymentUrl->currency == "EUR");
        $this->assertNull(self::$paymentUrl->customData);
        $this->assertNull(self::$paymentUrl->customer);
        $this->assertNull(self::$paymentUrl->email);
        $this->assertNull(self::$paymentUrl->firstName);
        $this->assertTrue(self::$paymentUrl->ipnUrl == "http://www.monsite.com/ipn");
        $this->assertNull(self::$paymentUrl->lastName);
        $this->assertNull(self::$paymentUrl->order);
        $this->assertNull(self::$paymentUrl->returnUrl);
    }

    /**
     * @expectedException ParametersNotSetException
     */
    public function testGenerateUrlParametersNotSetException() {
        self::$paymentUrl->generateUrl();
    }

    /**
     * @expectedException MalformedURLException
     */
    public function testGenerateUrlMalformedIpnUrl() {
        Payplug::setConfig(Parameters::loadFromFile(__DIR__ . "/PaymentUrlTest_parameters.json"));
        self::$paymentUrl->ipnUrl = "www.monsite.com/ipn";

        self::$paymentUrl->generateUrl();
    }

    /**
     * @expectedException MalformedURLException
     */
    public function testGenerateUrlMalformedReturnUrl() {
        self::$paymentUrl->ipnUrl = "https://www.monsite.com/ipn";
        self::$paymentUrl->returnUrl = "www.monsite.com/thankyou";

        self::$paymentUrl->generateUrl();
    }

    public function testGenerateUrlWithoutCustomerInfos() {
        self::$paymentUrl->returnUrl = "http://www.monsite.com/thankyou";

        $expectedUrl = "https://www.payplug.fr/p/MD1iqp-NEeO1vBIxQwfJEg==?data=YW1vdW50PTQyMDAmY3VycmVuY3k9RVVSJmlwbl91cmw9aHR0cHMlM0ElMkYlMkZ3d3cubW9uc2l0ZS5jb20lMkZpcG4mb3JpZ2luPStwYXlwbHVnLXBocDAuOStQSFA1LjMuMTAtMXVidW50dTMmcmV0dXJuX3VybD1odHRwJTNBJTJGJTJGd3d3Lm1vbnNpdGUuY29tJTJGdGhhbmt5b3U%3D&sign=AGK9fyGZSPaN0JVJoA5bQKfo%2FL6MHmF3uLefAGueZv7rSAniJ9l6%2FwdeSPuLG8KHPGQ9lWl5w0ad0gss5BLGCdI0%2FrK0UAiPLeEziFJKAgNlnq%2BN3lyUBNf2SEwGknsHF8ws1u%2FhhHGaFgPGexsJQHJ3I%2F0nHVmlgMijqi5xWRyrqeEf0HvNOjsN9ybKcbQHR1sJncg%2FEvBj%2B374srdvUO%2FBYrijOsYdlLBTBuJPc7jzK%2B77B3uPCqZrWc7gwZv3dxnHMhSYcVJMHDzID3qybGf7xs9FwPuCvAPfNe90gOUklH2ak%2FX9Wap7JOK%2FLv7Ed7eeS9jnM7t7otsUZHt2EQ%3D%3D";
        $generatedUrl = self::$paymentUrl->generateUrl();

        $this->assertEquals($expectedUrl, $generatedUrl);
    }

    public function testGenerateUrlWithCustomerInfos() {
        self::$paymentUrl->email = "testlib@payplug.fr";
        self::$paymentUrl->firstName = "John";
        self::$paymentUrl->lastName = "Doe";

        $expectedUrl = "https://www.payplug.fr/p/MD1iqp-NEeO1vBIxQwfJEg==?data=YW1vdW50PTQyMDAmY3VycmVuY3k9RVVSJmVtYWlsPXRlc3RsaWIlNDBwYXlwbHVnLmZyJmZpcnN0X25hbWU9Sm9obiZpcG5fdXJsPWh0dHBzJTNBJTJGJTJGd3d3Lm1vbnNpdGUuY29tJTJGaXBuJmxhc3RfbmFtZT1Eb2Umb3JpZ2luPStwYXlwbHVnLXBocDAuOStQSFA1LjMuMTAtMXVidW50dTMmcmV0dXJuX3VybD1odHRwJTNBJTJGJTJGd3d3Lm1vbnNpdGUuY29tJTJGdGhhbmt5b3U%3D&sign=SBDnMp%2B7tDPcZoU%2BiIXJtv9wYpWoosZS0lIiZBZ6T78TOg4KilaTEHo22jug2CBWXvIPpoGoal50TWjJDUpJPi6VPCy0euAYB9Yvb7z8Bk9HlUVEiaIOgXr3iAmCbZ6q111l4wWVVv1AaFX1ODc7FroCBWl8Tor%2B5VKOIZcJD4FG5bqpxb2%2BAtylrKduVNfve5SBSI5I1D%2BgQgjSI88F4JYhEc4YeMB%2B8kitfW6hfLn0HSDTBtflfY%2BTkxLE2xGbwFmVFgrXnNuW0iQDyhnj7YU%2B8kfxaSjISSYSV8CEQ9qdN3FIjjVDjiNDmTcHUF6ZKdbQID9HH3Ypir0ZTmPB8Q%3D%3D";
        $generatedUrl = self::$paymentUrl->generateUrl();

        $this->assertEquals($expectedUrl, $generatedUrl);
    }
}

