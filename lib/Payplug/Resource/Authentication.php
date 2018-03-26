<?php
namespace Payplug\Login;
use Payplug;

/**
 * 
 */
class Authentication
{
    /**
     * Retrieve existing API keys for an user, using his email and password.
     *
     * @param   string $email the user email
     * @param   string $password the user password
     *
     * @return  null|Payplug\Resource\APIResource|Card the card object
     *
     * @throws  Payplug\Exception\ConfigurationNotSetException
     */
    public static function getKeysByLogin($email, $password)
    {
        $data = array
        (
            'email' => $email,
            'password' => $password
        );

        $httpClient = new Payplug\Core\HttpClient(null);
        $response = $httpClient->get(Payplug\Core\APIRoutes::getRoute(Payplug\Core\APIRoutes::KEY_RESOURCE), $data, false);

        return $response;
    }

    public function login($email, $password)
    {
        $data = array
        (
            'email' => $email,
            'password' => $password
        );
        $data_string = PayplugBackward::jsonEncode($data);

        $url = $this->api_url.$this->routes['login'];
        $curl_version = curl_version();
        $process = curl_init($url);
        curl_setopt(
            $process,
            CURLOPT_HTTPHEADER,
            array('Content-Type:application/json',
                'Content-Length: '.PayplugBackward::strlen($data_string))
        );
        curl_setopt($process, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($process, CURLOPT_POST, true);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($process, CURLINFO_HEADER_OUT, true);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, true);
        # >= 7.26 to 7.28.1 add a notice message for value 1 will be remove
        curl_setopt(
            $process,
            CURLOPT_SSL_VERIFYHOST,
            (version_compare($curl_version['version'], '7.21', '<') ? true : 2)
        );
        curl_setopt($process, CURLOPT_CAINFO, realpath(dirname(__FILE__).'/cacert.pem')); //work only wiht cURL 7.10+
        $answer = curl_exec($process);
        $error_curl = curl_errno($process);

        curl_close($process);
        //d(PayplugBackward::jsonDecode($answer));
        if ($error_curl == 0) {
            $json_answer = PayplugBackward::jsonDecode($answer);

            if ($this->setApiKeysbyJsonResponse($json_answer)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
