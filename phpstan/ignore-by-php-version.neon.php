<?php declare(strict_types = 1);

$includes = [];
if (PHP_VERSION_ID < 80000) {
    $includes[] = __DIR__ . '/no-typed-prop.neon';
    $includes[] = __DIR__ . '/no-attributes.neon';
    $includes[] = __DIR__ . '/no-promoted-properties.neon';
}
if (PHP_VERSION_ID < 80100) {
    $includes[] = __DIR__ . '/no-enum.neon';
}

if (PHP_VERSION_ID >= 80200) {
    $includes[] = __DIR__ . '/no-info-in-reflection.neon';
}

$config = [];
$config['includes'] = $includes;
$config['parameters']['phpVersion'] = PHP_VERSION_ID;

return $config;
