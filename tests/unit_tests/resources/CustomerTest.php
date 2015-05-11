<?php

require_once 'lib/PayPlug.php';

class CustomerUnitTest extends PHPUnit_Framework_TestCase {
    public function testCreateCustomerFromAttributes()
    {
        $customer = PayPlug_Customer::fromAttributes(array(
            'email'         => 'john.doe@example.com',
            'first_name'    => 'John',
            'last_name'     => 'Doe',
            'address1'      => 'rue abc',
            'address2'      => 'cba',
            'postcode'      => '12345',
            'country'       => 'FR'
        ));

        $this->assertEquals('john.doe@example.com', $customer->getAttribute('email'));
        $this->assertEquals('John', $customer->getAttribute('first_name'));
        $this->assertEquals('Doe', $customer->getAttribute('last_name'));
        $this->assertEquals('rue abc', $customer->getAttribute('address1'));
        $this->assertEquals('cba', $customer->getAttribute('address2'));
        $this->assertEquals('12345', $customer->getAttribute('postcode'));
        $this->assertEquals('FR', $customer->getAttribute('country'));
    }
}
