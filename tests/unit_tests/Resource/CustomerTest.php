<?php
namespace Payplug\Resource;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class CustomerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateCustomerFromAttributes()
    {
        $customer = \Payplug\Resource\Customer::fromAttributes(array(
            'email'         => 'john.doe@example.com',
            'first_name'    => 'John',
            'last_name'     => 'Doe',
            'address1'      => 'rue abc',
            'address2'      => 'cba',
            'postcode'      => '12345',
            'country'       => 'FR'
        ));

        $this->assertEquals('john.doe@example.com', $customer->email);
        $this->assertEquals('John', $customer->first_name);
        $this->assertEquals('Doe', $customer->last_name);
        $this->assertEquals('rue abc', $customer->address1);
        $this->assertEquals('cba', $customer->address2);
        $this->assertEquals('12345', $customer->postcode);
        $this->assertEquals('FR', $customer->country);
    }
}
