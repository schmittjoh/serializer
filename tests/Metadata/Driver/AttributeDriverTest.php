<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\AttributeDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\ObjectWithOnlyLifecycleCallbacks;
use Metadata\Driver\DriverInterface;
use Metadata\MethodMetadata;

class AttributeDriverTest extends AnnotationDriverTest
{
    protected function setUp(): void
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Attributes are available only on php 8 or higher');
        }

        parent::setUp();
    }

    public function testLifeCycleCallbacks()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass(ObjectWithOnlyLifecycleCallbacks::class));

        $c = new ClassMetadata(ObjectWithOnlyLifecycleCallbacks::class);
        $c->preSerializeMethods[] = new MethodMetadata(ObjectWithOnlyLifecycleCallbacks::class, 'prepareForSerialization');
        $c->preSerializeMethods[] = new MethodMetadata(ObjectWithOnlyLifecycleCallbacks::class, 'prepareForSerialization');
        self::assertEquals($c->preSerializeMethods, $m->preSerializeMethods);

        $c->postSerializeMethods[] = new MethodMetadata(ObjectWithOnlyLifecycleCallbacks::class, 'cleanUpAfterSerialization');
        $c->postSerializeMethods[] = new MethodMetadata(ObjectWithOnlyLifecycleCallbacks::class, 'cleanUpAfterSerialization');
        self::assertEquals($c->postSerializeMethods, $m->postSerializeMethods);

        $c->postDeserializeMethods[] = new MethodMetadata(ObjectWithOnlyLifecycleCallbacks::class, 'afterDeserialization');
        $c->postDeserializeMethods[] = new MethodMetadata(ObjectWithOnlyLifecycleCallbacks::class, 'afterDeserialization');
        self::assertEquals($c->postDeserializeMethods, $m->postDeserializeMethods);
    }

    protected function getDriver(?string $subDir = null, bool $addUnderscoreDir = true): DriverInterface
    {
        $annotationsDriver = new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());
        $attributesAnnotationDriver = new AnnotationDriver(new AttributeDriver\AttributeReader(), new IdenticalPropertyNamingStrategy(), null, $this->getExpressionEvaluator());

        return new AttributeDriver($attributesAnnotationDriver, $annotationsDriver);
    }
}
