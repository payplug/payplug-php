<?php
spl_autoload_register(function ($class) {
    if (strpos($class, 'Payplug') !== 0) {
        return;
    }

    $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    if (file_exists($file)) {
        require($file);
    }
});
