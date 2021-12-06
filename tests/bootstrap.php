<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Tests\Util\DeprecationLogger;

(static function () {
    if (!is_file($autoloadFile = __DIR__ . '/../vendor/autoload.php')) {
        throw new RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
    }

    require $autoloadFile;

    AnnotationRegistry::registerLoader('class_exists');
})();


DeprecationLogger::register();
