<?php
spl_autoload_register(function ($class) {
    if (strpos($class, 'Payplug') !== 0) {
        return;
    }

    $deprecatedClasses = array(
        'Payplug\Exception\PayPlugException' => 'Payplug\Exception\PayplugException',
        'Payplug\Exception\PayPlugServerException' => 'Payplug\Exception\PayplugServerException',
    );

    foreach ($deprecatedClasses as $deprecatedClass => $substitutionClass) {
        if ($class === $deprecatedClass) {
            trigger_error(
                sprintf(
                    '\\%s is deprecated and may be removed in the near future. Use \\%s instead.',
                    $deprecatedClass, $substitutionClass
                ),
                E_USER_DEPRECATED
            );
            $class = $substitutionClass;
            break;
        }
    }

    $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    if (file_exists($file)) {
        require($file);
    }
});