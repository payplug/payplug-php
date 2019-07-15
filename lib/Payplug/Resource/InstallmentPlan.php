<?php
namespace Payplug\Resource;
use Payplug;

/**
 * An InstallmentPlan
 */
class InstallmentPlan extends APIResource implements IVerifiableAPIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  InstallmentPlan The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new InstallmentPlan();
        $object->initialize($attributes);
        return $object;
    }

    /**
     * Initializes the resource.
     * This method must be overridden when the resource has objects as attributes.
     *
     * @param   array   $attributes the attributes to initialize.
     */
    protected function initialize(array $attributes)
    {
        parent::initialize($attributes);

        /*
        * @deprecated No longer use by API, use billing and shipping instead
        */
        if (isset($attributes['customer'])) {
            $this->customer = PaymentCustomer::fromAttributes($attributes['customer']);
        }
        if (isset($attributes['billing'])) {
            $this->billing = PaymentBilling::fromAttributes($attributes['billing']);
        }
        if (isset($attributes['shipping'])) {
            $this->shipping = PaymentShipping::fromAttributes($attributes['shipping']);
        }
        if (isset($attributes['failure'])) {
            $this->failure = PaymentPaymentFailure::fromAttributes($attributes['failure']);
        }
        if (isset($attributes['hosted_payment'])) {
            $this->hosted_payment = PaymentHostedPayment::fromAttributes($attributes['hosted_payment']);
        }
        if (isset($attributes['notification'])) {
            $this->notification = PaymentNotification::fromAttributes($attributes['notification']);
        }
        $schedules = array();
        if (isset($attributes['schedule'])) {
            foreach ($attributes['schedule'] as &$schedule) {
                $schedules[] = InstallmentPlanSchedule::fromAttributes($schedule);
            }
        }
        $this->schedule = $schedules;
    }

    /**
     * List the payments of this installment plan.
     *
     * @param   Payplug\Payplug     $payplug    the client configuration
     *
     * @return  null|Payment[]   the array of payments of this installment plan
     *
     * @throws  Payplug\Exception\UndefinedAttributeException
     * @throws  Payplug\Exception\UnexpectedAPIResponseException
     */
    public function listPayments(Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        if (!array_key_exists('id', $this->getAttributes())) {
            throw new Payplug\Exception\UndefinedAttributeException(
                "This installment plan object has no id. You can't list payments on it.");
        }
        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->get(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::INSTALLMENT_PLAN_RESOURCE, $this->id)
        );

        $payments = array();
        foreach ($response['httpResponse']['schedule'] as $schedule) {
            foreach ($schedule['payment_ids'] as $payment_id) {
                $payments[$payment_id] = Payment::retrieve($payment_id, $payplug);
            }
        }

        return $payments;

    }

    /**
     * Aborts an InstallmentPlan.
     *
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  null|InstallmentPlan the aborted installment plan or null on error
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public function abort(Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->patch(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::INSTALLMENT_PLAN_RESOURCE, $this->id),
            array('aborted' => true)
        );

        return InstallmentPlan::fromAttributes($response['httpResponse']);
    }

    /**
     * Retrieves an InstallmentPlan.
     *
     * @param   string             $installmentPlanId  the installment plan ID
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  null|InstallmentPlan the retrieved installment plan or null on error
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function retrieve($installmentPlanId, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->get(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::INSTALLMENT_PLAN_RESOURCE,
                $installmentPlanId)
        );

        return InstallmentPlan::fromAttributes($response['httpResponse']);
    }

    /**
     * Creates an InstallmentPlan.
     *
     * @param   array               $data       API data for payment creation
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  null|InstallmentPlan the created installment plan instance
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function create(array $data, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->post(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::INSTALLMENT_PLAN_RESOURCE),
            $data
        );

        return InstallmentPlan::fromAttributes($response['httpResponse']);
    }

    /**
     * Returns an API resource that you can trust.
     *
     * @param   Payplug\Payplug $payplug the client configuration.
     *
     * @return  Payplug\Resource\APIResource The consistent API resource.
     *
     * @throws  Payplug\Exception\UndefinedAttributeException when the local resource is invalid.
     */
    function getConsistentResource(Payplug\Payplug $payplug = null)
    {
        if (!array_key_exists('id', $this->_attributes)) {
            throw new Payplug\Exception\UndefinedAttributeException(
                'The id of the installment plan is not set.');
        }

        return InstallmentPlan::retrieve($this->_attributes['id'], $payplug);
    }
}
