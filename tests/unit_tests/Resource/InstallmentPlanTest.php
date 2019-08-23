<?php
namespace Payplug\Resource;
use Payplug;

/**
 * @group unit
 * @group ci
 * @group recommended
 */
class InstallmentPlanTest extends \PHPUnit_Framework_TestCase
{
    private $_requestMock;
    private $_configuration;

    protected function setUp()
    {
        $this->_configuration = new Payplug\Payplug('abc');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        Payplug\Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    protected function setUpTwice()
    {
        $this->_configuration = new Payplug\Payplug('abc','1970-01-01');
        Payplug\Payplug::setDefaultConfiguration($this->_configuration);

        $this->_requestMock = $this->getMock('\Payplug\Core\IHttpRequest');
        Payplug\Core\HttpClient::$REQUEST_HANDLER = $this->_requestMock;
    }

    public function testCreateRetroInstallmentPlanFromAttributes()
    {
        $installment_plan = InstallmentPlan::fromAttributes(array(
            'id'                => 'inst_123456',
            'object'            => 'installment_plan',
            'is_live'           => true,
            'currency'          => 'EUR',
            'created_at'        => 1410437760,
            'is_active'         => true,
            'is_fully_paid'     => false,
            'schedule'          => array(
                array('date' => '2018-01-01',
                      'amount' => 10000,
                      'payment_ids' => array('pay_123', 'pay_456')),
                array('date' => '2018-02-01',
                      'amount' => 10000,
                      'payment_ids' => array('pay_789')),
                array('date' => '2018-03-01',
                      'amount' => 5000,
                      'payment_ids' => array())
            ),
            'failure'           => null,
            'customer'          => array(
                'email'             => 'name@customer.net',
                'first_name'        => 'John',
                'last_name'         => 'Doe'
            ),
            'hosted_payment'    => array(
                'payment_url'       => 'https://www.payplug.com/p/b9868d18546711e490c612314307c934',
                'return_url'        => 'http://yourwebsite.com/payplug_return?someid=11235',
                'cancel_url'        => 'http://yourwebsite.com/payplug_cancel?someid=81321',
            ),
            'notification'      => array(
                'url'               => 'http://yourwebsite.com/payplug_ipn',
                'response_code'     => 200
            ),
            'metadata'          => array(
                'a_custom_field'    => 'a custom value',
                'another_key'       => 'another value'
            )
        ));

        $this->assertEquals('inst_123456', $installment_plan->id);
        $this->assertEquals('installment_plan', $installment_plan->object);
        $this->assertEquals(true, $installment_plan->is_live);
        $this->assertEquals('EUR', $installment_plan->currency);
        $this->assertEquals(1410437760, $installment_plan->created_at);
        $this->assertEquals(true, $installment_plan->is_active);
        $this->assertEquals(false, $installment_plan->is_fully_paid);

        // Schedule
        $this->assertEquals('2018-01-01', $installment_plan->schedule[0]->date);
        $this->assertEquals(10000, $installment_plan->schedule[0]->amount);
        $this->assertEquals(array('pay_123', 'pay_456'), $installment_plan->schedule[0]->payment_ids);
        $this->assertEquals('2018-02-01', $installment_plan->schedule[1]->date);
        $this->assertEquals(10000, $installment_plan->schedule[1]->amount);
        $this->assertEquals(array('pay_789'), $installment_plan->schedule[1]->payment_ids);
        $this->assertEquals('2018-03-01', $installment_plan->schedule[2]->date);
        $this->assertEquals(5000, $installment_plan->schedule[2]->amount);
        $this->assertEquals(array(), $installment_plan->schedule[2]->payment_ids);

        $this->assertNull($installment_plan->failure);

        // Customer
        $this->assertEquals('name@customer.net', $installment_plan->customer->email);
        $this->assertEquals('John', $installment_plan->customer->first_name);
        $this->assertEquals('Doe', $installment_plan->customer->last_name);

       // Hosted payment
        $this->assertEquals('https://www.payplug.com/p/b9868d18546711e490c612314307c934', $installment_plan->hosted_payment->payment_url);
        $this->assertEquals('http://yourwebsite.com/payplug_return?someid=11235', $installment_plan->hosted_payment->return_url);
        $this->assertEquals('http://yourwebsite.com/payplug_cancel?someid=81321', $installment_plan->hosted_payment->cancel_url);

        // Notification
        $this->assertEquals('http://yourwebsite.com/payplug_ipn', $installment_plan->notification->url);
        $this->assertEquals(200, $installment_plan->notification->response_code);


        $this->assertEquals('a custom value', $installment_plan->metadata['a_custom_field']);
        $this->assertEquals('another value', $installment_plan->metadata['another_key']);
    }

    public function testCreateCompleteInstallmentPlanFromAttributes()
    {
        $installment_plan = InstallmentPlan::fromAttributes(array(
            'id'                => 'inst_123456',
            'object'            => 'installment_plan',
            'is_live'           => true,
            'currency'          => 'EUR',
            'created_at'        => 1410437760,
            'is_active'         => true,
            'is_fully_paid'     => false,
            'schedule'          => array(
                array('date' => '2018-01-01',
                      'amount' => 10000,
                      'payment_ids' => array('pay_123', 'pay_456')),
                array('date' => '2018-02-01',
                      'amount' => 10000,
                      'payment_ids' => array('pay_789')),
                array('date' => '2018-03-01',
                      'amount' => 5000,
                      'payment_ids' => array())
            ),
            'failure'           => null,
            'customer'          => array(
                'email'             => 'name@customer.net',
                'first_name'        => 'John',
                'last_name'         => 'Doe'
            ),
            'billing'          => array(
                "title" => "Mr",
                "first_name" => "John",
                "last_name" => "Doe",
                "email" => "name@customer.net",
                "phone_number" => "0123456789",
                "address1" => "77 rue la Boétie",
                "address2" => null,
                "company_name" => "PayPlug",
                "postcode" => "75008",
                "city" => "Paris",
                "state" => null,
                "country" => "FR",
                "language" => "fr"
            ),
            'shipping'          => array(
                "title" => "Mr",
                "first_name" => "John",
                "last_name" => "Doe",
                "email" => "name@customer.net",
                "phone_number" => "0123456789",
                "address1" => "77 rue la Boétie",
                "address2" => null,
                "company_name" => "PayPlug",
                "postcode" => "75008",
                "city" => "Paris",
                "state" => null,
                "country" => "FR",
                "language" => "fr"
            ),
            'hosted_payment'    => array(
                'payment_url'       => 'https://www.payplug.com/p/b9868d18546711e490c612314307c934',
                'return_url'        => 'http://yourwebsite.com/payplug_return?someid=11235',
                'cancel_url'        => 'http://yourwebsite.com/payplug_cancel?someid=81321',
            ),
            'notification'      => array(
                'url'               => 'http://yourwebsite.com/payplug_ipn',
                'response_code'     => 200
            ),
            'metadata'          => array(
                'a_custom_field'    => 'a custom value',
                'another_key'       => 'another value'
            )
        ));

        $this->assertEquals('inst_123456', $installment_plan->id);
        $this->assertEquals('installment_plan', $installment_plan->object);
        $this->assertEquals(true, $installment_plan->is_live);
        $this->assertEquals('EUR', $installment_plan->currency);
        $this->assertEquals(1410437760, $installment_plan->created_at);
        $this->assertEquals(true, $installment_plan->is_active);
        $this->assertEquals(false, $installment_plan->is_fully_paid);

        // Schedule
        $this->assertEquals('2018-01-01', $installment_plan->schedule[0]->date);
        $this->assertEquals(10000, $installment_plan->schedule[0]->amount);
        $this->assertEquals(array('pay_123', 'pay_456'), $installment_plan->schedule[0]->payment_ids);
        $this->assertEquals('2018-02-01', $installment_plan->schedule[1]->date);
        $this->assertEquals(10000, $installment_plan->schedule[1]->amount);
        $this->assertEquals(array('pay_789'), $installment_plan->schedule[1]->payment_ids);
        $this->assertEquals('2018-03-01', $installment_plan->schedule[2]->date);
        $this->assertEquals(5000, $installment_plan->schedule[2]->amount);
        $this->assertEquals(array(), $installment_plan->schedule[2]->payment_ids);

        $this->assertNull($installment_plan->failure);

        // Customer
        $this->assertEquals('name@customer.net', $installment_plan->customer->email);
        $this->assertEquals('John', $installment_plan->customer->first_name);
        $this->assertEquals('Doe', $installment_plan->customer->last_name);

        // Billing / Shiping
        $this->assertEquals('Mr', $installment_plan->shipping->title);
        $this->assertEquals('John', $installment_plan->billing->first_name);
        $this->assertEquals('Doe', $installment_plan->shipping->last_name);
        $this->assertEquals('name@customer.net', $installment_plan->billing->email);
        $this->assertEquals('0123456789', $installment_plan->shipping->phone_number);
        $this->assertEquals('77 rue la Boétie', $installment_plan->billing->address1);
        $this->assertEquals(null, $installment_plan->shipping->address2);
        $this->assertEquals('PayPlug', $installment_plan->billing->company_name);
        $this->assertEquals('75008', $installment_plan->shipping->postcode);
        $this->assertEquals('Paris', $installment_plan->billing->city);
        $this->assertEquals(null, $installment_plan->billing->state);
        $this->assertEquals('FR', $installment_plan->shipping->country);
        $this->assertEquals('fr', $installment_plan->billing->language);

       // Hosted payment
        $this->assertEquals('https://www.payplug.com/p/b9868d18546711e490c612314307c934', $installment_plan->hosted_payment->payment_url);
        $this->assertEquals('http://yourwebsite.com/payplug_return?someid=11235', $installment_plan->hosted_payment->return_url);
        $this->assertEquals('http://yourwebsite.com/payplug_cancel?someid=81321', $installment_plan->hosted_payment->cancel_url);

        // Notification
        $this->assertEquals('http://yourwebsite.com/payplug_ipn', $installment_plan->notification->url);
        $this->assertEquals(200, $installment_plan->notification->response_code);


        $this->assertEquals('a custom value', $installment_plan->metadata['a_custom_field']);
        $this->assertEquals('another value', $installment_plan->metadata['another_key']);
    }

    public function testCreateInstallmentPlanFromAttributes()
    {
        $installment_plan = InstallmentPlan::fromAttributes(array(
            'id'                => 'inst_123456',
            'object'            => 'installment_plan',
            'is_live'           => true,
            'currency'          => 'EUR',
            'created_at'        => 1410437760,
            'is_active'         => true,
            'is_fully_paid'     => false,
            'schedule'          => array(
                array('date' => '2018-01-01',
                      'amount' => 10000,
                      'payment_ids' => array('pay_123', 'pay_456')),
                array('date' => '2018-02-01',
                      'amount' => 10000,
                      'payment_ids' => array('pay_789')),
                array('date' => '2018-03-01',
                      'amount' => 5000,
                      'payment_ids' => array())
            ),
            'failure'           => null,
            'billing'          => array(
                "title" => "Mr",
                "first_name" => "John",
                "last_name" => "Doe",
                "email" => "name@customer.net",
                "phone_number" => "0123456789",
                "address1" => "77 rue la Boétie",
                "address2" => null,
                "company_name" => "PayPlug",
                "postcode" => "75008",
                "city" => "Paris",
                "state" => null,
                "country" => "FR",
                "language" => "fr"
            ),
            'shipping'          => array(
                "title" => "Mr",
                "first_name" => "John",
                "last_name" => "Doe",
                "email" => "name@customer.net",
                "phone_number" => "0123456789",
                "address1" => "77 rue la Boétie",
                "address2" => null,
                "company_name" => "PayPlug",
                "postcode" => "75008",
                "city" => "Paris",
                "state" => null,
                "country" => "FR",
                "language" => "fr"
            ),
            'hosted_payment'    => array(
                'payment_url'       => 'https://www.payplug.com/p/b9868d18546711e490c612314307c934',
                'return_url'        => 'http://yourwebsite.com/payplug_return?someid=11235',
                'cancel_url'        => 'http://yourwebsite.com/payplug_cancel?someid=81321',
            ),
            'notification'      => array(
                'url'               => 'http://yourwebsite.com/payplug_ipn',
                'response_code'     => 200
            ),
            'metadata'          => array(
                'a_custom_field'    => 'a custom value',
                'another_key'       => 'another value'
            )
        ));

        $this->assertEquals('inst_123456', $installment_plan->id);
        $this->assertEquals('installment_plan', $installment_plan->object);
        $this->assertEquals(true, $installment_plan->is_live);
        $this->assertEquals('EUR', $installment_plan->currency);
        $this->assertEquals(1410437760, $installment_plan->created_at);
        $this->assertEquals(true, $installment_plan->is_active);
        $this->assertEquals(false, $installment_plan->is_fully_paid);

        // Schedule
        $this->assertEquals('2018-01-01', $installment_plan->schedule[0]->date);
        $this->assertEquals(10000, $installment_plan->schedule[0]->amount);
        $this->assertEquals(array('pay_123', 'pay_456'), $installment_plan->schedule[0]->payment_ids);
        $this->assertEquals('2018-02-01', $installment_plan->schedule[1]->date);
        $this->assertEquals(10000, $installment_plan->schedule[1]->amount);
        $this->assertEquals(array('pay_789'), $installment_plan->schedule[1]->payment_ids);
        $this->assertEquals('2018-03-01', $installment_plan->schedule[2]->date);
        $this->assertEquals(5000, $installment_plan->schedule[2]->amount);
        $this->assertEquals(array(), $installment_plan->schedule[2]->payment_ids);

        $this->assertNull($installment_plan->failure);

        // Billing / Shiping
        $this->assertEquals('Mr', $installment_plan->shipping->title);
        $this->assertEquals('John', $installment_plan->billing->first_name);
        $this->assertEquals('Doe', $installment_plan->shipping->last_name);
        $this->assertEquals('name@customer.net', $installment_plan->billing->email);
        $this->assertEquals('0123456789', $installment_plan->shipping->phone_number);
        $this->assertEquals('77 rue la Boétie', $installment_plan->billing->address1);
        $this->assertEquals(null, $installment_plan->shipping->address2);
        $this->assertEquals('PayPlug', $installment_plan->billing->company_name);
        $this->assertEquals('75008', $installment_plan->shipping->postcode);
        $this->assertEquals('Paris', $installment_plan->billing->city);
        $this->assertEquals(null, $installment_plan->billing->state);
        $this->assertEquals('FR', $installment_plan->shipping->country);
        $this->assertEquals('fr', $installment_plan->billing->language);

       // Hosted payment
        $this->assertEquals('https://www.payplug.com/p/b9868d18546711e490c612314307c934', $installment_plan->hosted_payment->payment_url);
        $this->assertEquals('http://yourwebsite.com/payplug_return?someid=11235', $installment_plan->hosted_payment->return_url);
        $this->assertEquals('http://yourwebsite.com/payplug_cancel?someid=81321', $installment_plan->hosted_payment->cancel_url);

        // Notification
        $this->assertEquals('http://yourwebsite.com/payplug_ipn', $installment_plan->notification->url);
        $this->assertEquals(200, $installment_plan->notification->response_code);


        $this->assertEquals('a custom value', $installment_plan->metadata['a_custom_field']);
        $this->assertEquals('another value', $installment_plan->metadata['another_key']);
    }

    public function testInstallmentPlanCreate()
    {
        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));

        $this->_requestMock
            ->expects($this->atLeastOnce())
            ->method('setopt')
            ->will($this->returnCallback(function($option, $value = null) {
                switch($option) {
                    case CURLOPT_POSTFIELDS:
                        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = json_decode($value, true);
                        return true;
                }
                return true;
            }));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $data = array(
            'currency'          => 'EUR',
            'schedule'          => array(
                array('date' => '2018-01-01',
                      'amount' => 10000),
                array('date' => '2018-02-01',
                      'amount' => 10000),
                array('date' => '2018-03-01',
                      'amount' => 5000),
            ),
            'customer'          => array(
                'email'         => 'john.doe@example.com',
                'first_name'    => 'John',
                'last_name'     => 'Doe'
            ),
            'billing'          => array(
                'title' => 'Mr',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'name@customer.net',
                'phone_number' => '0123456789',
                'address1' => '77 rue la Boétie',
                'address2' => 'ul',
                'company_name' => 'PayPlug',
                'postcode' => '75008',
                'city' => 'Paris',
                'state' => 'ul',
                'country' => 'FR',
                'language' => 'fr'
            ),
            'shipping'          => array(
                'title' => 'Mr',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'name@customer.net',
                'phone_number' => '0123456789',
                'address1' => '77 rue la Boétie',
                'address2' => 'ul',
                'company_name' => 'PayPlug',
                'postcode' => '75008',
                'city' => 'Paris',
                'state' => 'ul',
                'country' => 'FR',
                'language' => 'fr'
            ),
            'hosted_payment'    => array(
                'return_url'        => 'https://www.example.com/thank_you_for_your_payment.html',
                'cancel_url'        => 'https://www.example.com/so_bad_it_didnt_make_it.html'
            ),
            'notification_url'  => 'http://www.example.org/callbackURL'
        );

        $payment = InstallmentPlan::create($data);

        $this->assertEquals($data, $GLOBALS['CURLOPT_POSTFIELDS_DATA']);
        $this->assertEquals('ok', $payment->status);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testInstallmentPlanAbort()
    {
        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));

        $this->_requestMock
            ->expects($this->atLeastOnce())
            ->method('setopt')
            ->will($this->returnCallback(function($option, $value = null) {
                switch($option) {
                    case CURLOPT_POSTFIELDS:
                        $GLOBALS['CURLOPT_POSTFIELDS_DATA'] = json_decode($value, true);
                        return true;
                }
                return true;
            }));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $payment = Payplug\InstallmentPlan::abort('a_payment_id');

        $this->assertEquals($GLOBALS['CURLOPT_POSTFIELDS_DATA'], array('aborted' => true));
        $this->assertEquals('ok', $payment->status);

        unset($GLOBALS['CURLOPT_POSTFIELDS_DATA']);
    }

    public function testInstallmentPlanRetrieve()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = null;

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"status":"ok"}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback(function($option, $value = null) {
                switch($option) {
                    case CURLOPT_URL:
                        $GLOBALS['CURLOPT_URL_DATA'] = $value;
                        return true;
                }
                return true;
            }));

        $installment_plan = InstallmentPlan::retrieve('a_installment_plan');

        $this->assertStringEndsWith('a_installment_plan', $GLOBALS['CURLOPT_URL_DATA']);
        $this->assertEquals('ok', $installment_plan->status);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testInstallmentPlanListPaymentsWhenPaymentIsInvalid()
    {
        $this->setExpectedException('\PayPlug\Exception\UndefinedAttributeException');
        $installment_plan = InstallmentPlan::fromAttributes(array('fake' => 'payment'));
        $installment_plan->listPayments();
    }

    public function testInstallmentPlanListPayments()
    {
        $GLOBALS['CURLOPT_URL_DATA'] = array();
        $data = json_encode(
            array('schedule' => array(
                    array('date' => '2018-01-01',
                          'amount' => 10000,
                          'payment_ids' => array('pay_123', 'pay_456')),
                    array('date' => '2018-02-01',
                          'amount' => 10000,
                          'payment_ids' => array('pay_789')),
                    array('date' => '2018-03-01',
                          'amount' => 5000,
                          'payment_ids' => array())
            )));

        $this->_requestMock
            ->expects($this->exactly(4))
            ->method('exec')
            ->will($this->onConsecutiveCalls(
                $data,
                // Retrieve payment
                '{"id": "pay_123"}',
                '{"id": "pay_456"}',
                '{"id": "pay_789"}'
            ));


        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));
        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnCallback(function($option, $value = null) {
                switch($option) {
                    case CURLOPT_URL:
                        $GLOBALS['CURLOPT_URL_DATA'][] = $value;
                        return true;
                }
                return true;
            }));

        $installment_plan = InstallmentPlan::fromAttributes(array('id' => 'a_inst_id'));
        $payments = $installment_plan->listPayments();
        $this->assertEquals(3, count($payments));
        $this->assertTrue($payments['pay_123']->id === 'pay_123');
        $this->assertTrue($payments['pay_456']->id === 'pay_456');
        $this->assertTrue($payments['pay_789']->id === 'pay_789');
        $this->assertContains('a_inst_id', $GLOBALS['CURLOPT_URL_DATA'][0]);
        $this->assertContains('pay_123', $GLOBALS['CURLOPT_URL_DATA'][1]);
        $this->assertContains('pay_456', $GLOBALS['CURLOPT_URL_DATA'][2]);
        $this->assertContains('pay_789', $GLOBALS['CURLOPT_URL_DATA'][3]);

        unset($GLOBALS['CURLOPT_URL_DATA']);
    }

    public function testRetrieveConsistentInstallmentPlanWhenIdIsUndefined()
    {
        $this->setExpectedException('\PayPlug\Exception\UndefinedAttributeException');

        $installment_plan = InstallmentPlan::fromAttributes(array('this_installment_plan' => 'has_no_id'));
        $installment_plan->getConsistentResource();
    }

    public function testRetrieveConsistentInstallmentPlan()
    {
        function testRetrieveConsistentInstallmentPlan_getinfo($option) {
            switch($option) {
                case CURLINFO_HTTP_CODE:
                    return 200;
            }
            return null;
        }

        $this->_requestMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('{"id": "inst_345"}'));

        $this->_requestMock
            ->expects($this->any())
            ->method('setopt')
            ->will($this->returnValue(true));
        $this->_requestMock
            ->expects($this->any())
            ->method('getinfo')
            ->will($this->returnCallback(function($option) {
                switch($option) {
                    case CURLINFO_HTTP_CODE:
                        return 200;
                }
                return null;
            }));

        $installment_plan1 = InstallmentPlan::fromAttributes(array('id' => 'inst_123'));
        $installment_plan2 = $installment_plan1->getConsistentResource($this->_configuration);

        $this->assertEquals('inst_123', $installment_plan1->id);
        $this->assertEquals('inst_345', $installment_plan2->id);
    }
}
