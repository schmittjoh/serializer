<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver as DoctrineAnnotationDriver;
use Doctrine\ORM\Mapping\Driver\AttributeDriver as DoctrineAttributeDriver;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\AnnotationOrAttributeDriver;
use JMS\Serializer\Metadata\Driver\DoctrineTypeDriver;
use JMS\Serializer\Metadata\Driver\NullDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\BlogPost;
use JMS\Serializer\Tests\Fixtures\Doctrine\Embeddable\BlogPostWithEmbedded;
use JMS\Serializer\Tests\Fixtures\Doctrine\Entity\Author;
use JMS\Serializer\Tests\Fixtures\Doctrine\Entity\BlogPost as DoctrineBlogPost;
use JMS\Serializer\Tests\Fixtures\Doctrine\Entity\Comment;
use JMS\Serializer\Tests\Fixtures\ExcludePublicAccessor;
use Metadata\Driver\DriverChain;
use PHPUnit\Framework\TestCase;

class DoctrineDriverTest extends TestCase
{
    public function getMetadata()
    {
        $refClass = new \ReflectionClass(DoctrineBlogPost::class);

        return $this->getDoctrineDriver()->loadMetadataForClass($refClass);
    }

    public function testMetadataForEmbedded()
    {
        $refClass = new \ReflectionClass(BlogPostWithEmbedded::class);
        $meta = $this->getDoctrineDriver()->loadMetadataForClass($refClass);
        self::assertNotNull($meta);
    }

    public function testTypelessPropertyIsGivenTypeFromDoctrineMetadata()
    {
        $metadata = $this->getMetadata();

        self::assertEquals(
            ['name' => 'DateTime', 'params' => []],
            $metadata->propertyMetadata['createdAt']->type,
        );
    }

    public function testSingleValuedAssociationIsProperlyHinted()
    {
        $metadata = $this->getMetadata();
        self::assertEquals(
            ['name' => Author::class, 'params' => []],
            $metadata->propertyMetadata['author']->type,
        );
    }

    public function testMultiValuedAssociationIsProperlyHinted()
    {
        $metadata = $this->getMetadata();

        self::assertEquals(
            [
                'name' => 'ArrayCollection',
                'params' => [
                    ['name' => Comment::class, 'params' => []],
                ],
            ],
            $metadata->propertyMetadata['comments']->type,
        );
    }

    public function testTypeGuessByDoctrineIsOverwrittenByDelegateDriver()
    {
        $metadata = $this->getMetadata();

        // This would be guessed as boolean but we've overriden it to integer
        self::assertEquals(
            ['name' => 'integer', 'params' => []],
            $metadata->propertyMetadata['published']->type,
        );
    }

    public function testUnknownDoctrineTypeDoesNotResultInAGuess()
    {
        $metadata = $this->getMetadata();
        self::assertNull($metadata->propertyMetadata['slug']->type);
    }

    public function testNonDoctrineEntityClassIsNotModified()
    {
        // Note: Using regular BlogPost fixture here instead of Doctrine fixture
        // because it has no Doctrine metadata.
        $refClass = new \ReflectionClass(BlogPost::class);

        $plainMetadata = $this->getMetadataDriver()->loadMetadataForClass($refClass);
        $doctrineMetadata = $this->getDoctrineDriver()->loadMetadataForClass($refClass);

        // Do not compare timestamps
        if (abs($doctrineMetadata->createdAt - $plainMetadata->createdAt) < 2) {
            $plainMetadata->createdAt = $doctrineMetadata->createdAt;
        }

        self::assertEquals($plainMetadata, $doctrineMetadata);
    }

    public function testExcludePropertyNoPublicAccessorException()
    {
        $first = $this->getMetadataDriver()
            ->loadMetadataForClass(new \ReflectionClass(ExcludePublicAccessor::class));

        self::assertArrayHasKey('id', $first->propertyMetadata);
        self::assertArrayNotHasKey('iShallNotBeAccessed', $first->propertyMetadata);
    }

    public function testVirtualPropertiesAreNotModified()
    {
        $doctrineMetadata = $this->getMetadata();
        self::assertNull($doctrineMetadata->propertyMetadata['ref']->type);
    }

    public function testGuidPropertyIsGivenStringType()
    {
        $metadata = $this->getMetadata();

        self::assertEquals(
            ['name' => 'string', 'params' => []],
            $metadata->propertyMetadata['guid']->type,
        );
    }

    protected function getEntityManager(): EntityManager
    {
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir() . '/JMSDoctrineTestProxies');
        $config->setProxyNamespace('JMS\Tests\Proxies');

        if (PHP_VERSION_ID >= 80000 && class_exists(DoctrineAttributeDriver::class)) {
            $config->setMetadataDriverImpl(
                new DoctrineAttributeDriver([__DIR__ . '/../../Fixtures/Doctrine'], true),
            );
        } else {
            assert(class_exists(DoctrineAnnotationDriver::class));
            $config->setMetadataDriverImpl(
                new DoctrineAnnotationDriver(new AnnotationReader(), __DIR__ . '/../../Fixtures/Doctrine'),
            );
        }

        $conn = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        return new EntityManager($conn, $config);
    }

    public function getMetadataDriver()
    {
        $driver = new DriverChain();
        $namingStrategy = new IdenticalPropertyNamingStrategy();

        if (PHP_VERSION_ID >= 80000) {
            $driver->addDriver(new AnnotationOrAttributeDriver($namingStrategy));
        } else {
            $driver->addDriver(new AnnotationDriver(new AnnotationReader(), $namingStrategy));
        }

        $driver->addDriver(new NullDriver($namingStrategy));

        return $driver;
    }

    protected function getDoctrineDriver()
    {
        $registry = $this->getMockBuilder(ManagerRegistry::class)->getMock();
        $registry->expects($this->atLeastOnce())
            ->method('getManagerForClass')
            ->willReturn($this->getEntityManager());

        return new DoctrineTypeDriver(
            $this->getMetadataDriver(),
            $registry,
        );
    }
}
