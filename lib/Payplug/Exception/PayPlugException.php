<?php
namespace Payplug\Exception;

trigger_error(
    'PayPlugException is deprecated and may be removed in a near future. Use PayplugException instead.',
    E_USER_DEPRECATED
);

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'PayplugException.php');
