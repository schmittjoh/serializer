<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Metadata\Driver\DriverInterface;

class AnnotationDriverTest extends BaseAnnotationOrAttributeDriverTest
{
    protected function getDriver(?string $subDir = null, bool $addUnderscoreDir = true): DriverInterface
    {
        return new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());
    }
}
