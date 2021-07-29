<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\AttributeDriver;
use JMS\Serializer\Metadata\Driver\NullDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Metadata\Driver\DriverInterface;

class AttributeDriverWithNoFallbackTest extends AttributeDriverTest
{
    protected function getDriver(?string $subDir = null, bool $addUnderscoreDir = true): DriverInterface
    {
        $attributesAnnotationDriver = new AnnotationDriver(new AttributeDriver\AttributeReader(), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());

        return new AttributeDriver($attributesAnnotationDriver, new NullDriver());
    }
}
