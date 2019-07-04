<?php
namespace Payplug\Resource;
use Payplug;

/**
 * A Payment
 */
class Payment extends APIResource implements IVerifiableAPIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  Payment The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new Payment();
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

        if (isset($attributes['card'])) {
            $this->card = PaymentCard::fromAttributes($attributes['card']);
        }

        /*
        * @deprecated No longer used by API, use billing and shipping instead
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
        if (isset($attributes['hosted_payment'])) {
            $this->hosted_payment = PaymentHostedPayment::fromAttributes($attributes['hosted_payment']);
        }
        if (isset($attributes['failure'])) {
            $this->failure = PaymentPaymentFailure::fromAttributes($attributes['failure']);
        }
        if (isset($attributes['notification'])) {
            $this->notification = PaymentNotification::fromAttributes($attributes['notification']);
        }
        if (isset($attributes['authorization'])) {
            $this->authorization = PaymentAuthorization::fromAttributes($attributes['authorization']);
        }
    }

    /**
     * Open a refund on the payment.
     *
     * @param   array               $data       the refund data
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  Refund|null the opened refund instance
     *
     * @throws  Payplug\Exception\InvalidPaymentException when the id of the payment is invalid
     */
    public function refund(array $data = null, Payplug\Payplug $payplug = null)
    {
        if (!array_key_exists('id', $this->getAttributes())) {
            throw new Payplug\Exception\InvalidPaymentException("This payment object has no id. It can't be refunded.");
        }

        return Refund::create($this->id, $data, $payplug);
    }

    /**
     * List the refunds of this payment.
     *
     * @param   Payplug\Payplug     $payplug    the client configuration
     *
     * @return  null|Refund[]   the array of refunds of this payment
     *
     * @throws  Payplug\Exception\InvalidPaymentException
     * @throws  Payplug\Exception\UnexpectedAPIResponseException
     */
    public function listRefunds(Payplug\Payplug $payplug = null)
    {
        if (!array_key_exists('id', $this->getAttributes())) {
            throw new Payplug\Exception\InvalidPaymentException("This payment object has no id. You can't list refunds on it.");
        }

        return Refund::listRefunds($this->id, $payplug);
    }

    /**
     * Aborts a Payment.
     *
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  null|Payment the aborted payment or null on error
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
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::PAYMENT_RESOURCE, $this->id),
            array('aborted' => true)
        );

        return Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * Captures a Payment.
     * 
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  null|Payment the captured payment or null on error
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public function capture(Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->patch(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::PAYMENT_RESOURCE, $this->id),
            array('captured' => true)
        );

        return Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * Retrieves a Payment.
     *
     * @param   string             $paymentId  the payment ID
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  null|Payment the retrieved payment or null on error
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function retrieve($paymentId, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->get(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::PAYMENT_RESOURCE, $paymentId)
        );

        return Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * List payments.
     *
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @param   int                 $perPage    the number of results per page
     * @param   int                 $page       the page number
     * @return  null|Payment[]   the array of payments
     *
     * @throws  Payplug\Exception\InvalidPaymentException
     * @throws  Payplug\Exception\UnexpectedAPIResponseException
     */
    public static function listPayments($perPage = null, $page = null, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $pagination = array('per_page' => $perPage, 'page' => $page);
        $response = $httpClient->get(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::PAYMENT_RESOURCE, null, array(), $pagination)
        );

        if (!array_key_exists('data', $response['httpResponse'])
            || !is_array($response['httpResponse']['data'])) {
            throw new Payplug\Exception\UnexpectedAPIResponseException(
                "Expected 'data' key in API response.",
                $response['httpResponse']
            );
        }

        $payments = array();
        foreach ($response['httpResponse']['data'] as &$payment) {
            $payments[] = Payment::fromAttributes($payment);
        }

        return $payments;
    }

    /**
     * Creates a Payment.
     *
     * @param   array               $data       API data for payment creation
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  null|Payment the created payment instance
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
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::PAYMENT_RESOURCE),
            $data
        );

        return Payment::fromAttributes($response['httpResponse']);
    }

    /**
     * Update a Payment.
     *
     * @param   array               $data       API data for payment creation
     * @param   Payplug\Payplug    $payplug    the client configuration
     *
     * @return  null|Payment the updated payment instance
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public function update(array $data, Payplug\Payplug $payplug = null)
    {
        if ($payplug === null) {
            $payplug = Payplug\Payplug::getDefaultConfiguration();
        }

        $httpClient = new Payplug\Core\HttpClient($payplug);
        $response = $httpClient->patch(
            Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::PAYMENT_RESOURCE, $this->id),
            $data
        );

        return Payment::fromAttributes($response['httpResponse']);
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
            throw new Payplug\Exception\UndefinedAttributeException('The id of the payment is not set.');
        }

        return Payment::retrieve($this->_attributes['id'], $payplug);
    }
}
