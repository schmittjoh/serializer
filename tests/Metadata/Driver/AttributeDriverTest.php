<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\Driver\AttributeDriver;
use JMS\Serializer\Metadata\Driver\NullDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Metadata\Driver\DriverChain;
use Metadata\Driver\DriverInterface;

class AttributeDriverTest extends BaseAnnotationOrAttributeDriverTestCase
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
        $namingStrategy = new IdenticalPropertyNamingStrategy();

        return new DriverChain([
            new AttributeDriver($namingStrategy, null, $this->getExpressionEvaluator()),
            new NullDriver($namingStrategy),
        ]);
    }
}
