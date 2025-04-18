<?php
namespace Payplug\Resource;

/**
 * A Hosted Payment
 */
class PaymentHostedPayment extends APIResource
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  PaymentHostedPayment   The new resource.
     */
    public static function fromAttributes(array $attributes)
    {
        $object = new PaymentHostedPayment();
        $object->initialize($attributes);
        return $object;
    }

    /**
     * Create a Hosted Fields token.
     *
     * @return array
     */
	public static function createHFToken()
	{
		$httpClient = new Payplug\Core\HttpClient();
        try {
            $response = $httpClient->post(
              'https://staging-hosted-fields-front-service.notprod-cde-gwdl.gcp.dlns.io/service/tokenize',
              array(
                  'APIKEYID' => 'fadc44f6-b98b-4ea1-a8a0-50ab1d2e216f',
                  'SELECTEDBRAND' => 'mastercard',
                  'CARDVALIDITYDATE' => '12-28',
                  'CARDCVV' => '123',
                  'CARDCODE' => '5131080132762421'
              ),
              false,
              'uid=Ck8hGmfi3cbB7AFVAx5bAg==',
              array(
                  'Content-Type: text/plain',
                  'x-test-auto: true'
              )
            );

            return $response;
        } catch (\Exception $e) {
            return array();
        }
	}
}
