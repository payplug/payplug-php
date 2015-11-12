<?php
namespace Payplug\Exception;

trigger_error(
    'PayPlugServerException is deprecated and may be removed in a near future. Use PayplugServerException instead.',
    E_USER_DEPRECATED
);

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'PayplugServerException.php');
