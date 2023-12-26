<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\NullDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Metadata\Driver\DriverChain;
use Metadata\Driver\DriverInterface;

class AnnotationDriverTest extends BaseAnnotationOrAttributeDriverTestCase
{
    protected function getDriver(?string $subDir = null, bool $addUnderscoreDir = true): DriverInterface
    {
        $namingStrategy = new IdenticalPropertyNamingStrategy();

        return new DriverChain([
            new AnnotationDriver(new AnnotationReader(), $namingStrategy, null, $this->getExpressionEvaluator()),
            new NullDriver($namingStrategy),
        ]);
    }
}
