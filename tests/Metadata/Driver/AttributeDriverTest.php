<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\AttributeDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Metadata\Driver\DriverInterface;

class AttributeDriverTest extends AnnotationDriverTest
{
    protected function setUp(): void
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Attributes are available only on php 8 or higher');
        }

        parent::setUp();
    }

    protected function getDriver(?string $subDir = null, bool $addUnderscoreDir = true): DriverInterface
    {
        $annotationsDriver = new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());
        $attributesAnnotationDriver = new AnnotationDriver(new AttributeDriver\AttributeReader(), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());

        return new AttributeDriver($attributesAnnotationDriver, $annotationsDriver);
    }
}
