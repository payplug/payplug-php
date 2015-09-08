<?php

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__)));

require_once 'Payplug/Exception/PayPlugException.php';
require_once 'Payplug/Exception/ConfigurationNotSetException.php';
require_once 'Payplug/Exception/ConnectionException.php';
require_once 'Payplug/Exception/DependencyException.php';
require_once 'Payplug/Exception/HttpException.php';
require_once 'Payplug/Exception/InvalidPaymentException.php';
require_once 'Payplug/Exception/UndefinedAttributeException.php';
require_once 'Payplug/Exception/UnknownAPIResourceException.php';

require_once 'Payplug/Core/APIRoutes.php';
require_once 'Payplug/Core/Config.php';
require_once 'Payplug/Core/HttpClient.php';

require_once 'Payplug/Payplug.php';
require_once 'Payplug/Notification.php';

require_once 'Payplug/Resource/APIResource.php';
require_once 'Payplug/Resource/IVerifiableAPIResource.php';

require_once 'Payplug/Resource/Card.php';
require_once 'Payplug/Resource/Customer.php';
require_once 'Payplug/Resource/HostedPayment.php';
require_once 'Payplug/Resource/Notification.php';
require_once 'Payplug/Resource/Payment.php';
require_once 'Payplug/Resource/PaymentFailure.php';
require_once 'Payplug/Resource/Refund.php';

require_once 'Payplug/Payment.php';
require_once 'Payplug/Refund.php';
