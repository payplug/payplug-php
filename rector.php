<?php

declare(strict_types=1);

use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;

return Rector\Config\RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/lib',
    ])
    ->withPhpVersion(Rector\ValueObject\PhpVersion::PHP_84)
    ->withRules([
        ExplicitNullableParamTypeRector::class,
        CompleteDynamicPropertiesRector::class,
    ]);