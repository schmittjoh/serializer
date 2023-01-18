<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver as DoctrineDriver;
use Doctrine\ORM\Version as ORMVersion;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\DoctrineTypeDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Tests\Fixtures\Doctrine\Embeddable\BlogPostWithEmbedded;
use JMS\Serializer\Tests\Fixtures\Doctrine\Entity\BlogPost;
use JMS\Serializer\Tests\Fixtures\Doctrine\Enums\SuitEntity;
use JMS\Serializer\Tests\Fixtures\Enum\BackedSuit;
use JMS\Serializer\Tests\Fixtures\Enum\BackedSuitInt;
use PHPUnit\Framework\TestCase;

class DoctrineDriverTest extends TestCase
{
    public function getMetadata(string $class = BlogPost::class): ClassMetadata
    {
        $refClass = new \ReflectionClass($class);

        return $this->getDoctrineDriver()->loadMetadataForClass($refClass);
    }

    public function testMetadataForEnums()
    {
        if (PHP_VERSION_ID < 80100 || ORMVersion::compare('2.11') >= 0) {
            $this->markTestSkipped('Not using Doctrine PHP >= 8.1 ORM >= 2.11 with Enums entities');
        }

        $metadata = $this->getMetadata(SuitEntity::class);

        self::assertEquals(
            ['name' => 'enum', 'params' => [BackedSuitInt::class, 'value', 'integer']],
            $metadata->propertyMetadata['id']->type
        );
        self::assertEquals(
            ['name' => 'enum', 'params' => [BackedSuit::class, 'value', 'string']],
            $metadata->propertyMetadata['name']->type
        );
    }

    public function testMetadataForEmbedded()
    {
        if (ORMVersion::compare('2.5') >= 0) {
            $this->markTestSkipped('Not using Doctrine ORM >= 2.5 with Embedded entities');
        }

        $metadata = $this->getMetadata(BlogPostWithEmbedded::class);
        self::assertNotNull($metadata);
    }

    public function testTypelessPropertyIsGivenTypeFromDoctrineMetadata()
    {
        $metadata = $this->getMetadata();

        self::assertEquals(
            ['name' => 'DateTime', 'params' => []],
            $metadata->propertyMetadata['createdAt']->type
        );
    }

    public function testSingleValuedAssociationIsProperlyHinted()
    {
        $metadata = $this->getMetadata();
        self::assertEquals(
            ['name' => 'JMS\Serializer\Tests\Fixtures\Doctrine\Entity\Author', 'params' => []],
            $metadata->propertyMetadata['author']->type
        );
    }

    public function testMultiValuedAssociationIsProperlyHinted()
    {
        $metadata = $this->getMetadata();

        self::assertEquals(
            [
                'name' => 'ArrayCollection',
                'params' => [
                    ['name' => 'JMS\Serializer\Tests\Fixtures\Doctrine\Entity\Comment', 'params' => []],
                ],
            ],
            $metadata->propertyMetadata['comments']->type
        );
    }

    public function testTypeGuessByDoctrineIsOverwrittenByDelegateDriver()
    {
        $metadata = $this->getMetadata();

        // This would be guessed as boolean but we've overriden it to integer
        self::assertEquals(
            ['name' => 'integer', 'params' => []],
            $metadata->propertyMetadata['published']->type
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
        $refClass = new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost');

        $plainMetadata = $this->getAnnotationDriver()->loadMetadataForClass($refClass);
        $doctrineMetadata = $this->getDoctrineDriver()->loadMetadataForClass($refClass);

        // Do not compare timestamps
        if (abs($doctrineMetadata->createdAt - $plainMetadata->createdAt) < 2) {
            $plainMetadata->createdAt = $doctrineMetadata->createdAt;
        }

        self::assertEquals($plainMetadata, $doctrineMetadata);
    }

    public function testExcludePropertyNoPublicAccessorException()
    {
        $first = $this->getAnnotationDriver()
            ->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\ExcludePublicAccessor'));

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
            $metadata->propertyMetadata['guid']->type
        );
    }

    protected function getEntityManager()
    {
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir() . '/JMSDoctrineTestProxies');
        $config->setProxyNamespace('JMS\Tests\Proxies');
        $config->setMetadataDriverImpl(
            new DoctrineDriver(new AnnotationReader(), __DIR__ . '/../../Fixtures/Doctrine')
        );

        $conn = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        return EntityManager::create($conn, $config);
    }

    public function getAnnotationDriver()
    {
        return new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy());
    }

    protected function getDoctrineDriver()
    {
        $registry = $this->getMockBuilder(ManagerRegistry::class)->getMock();
        $registry->expects($this->atLeastOnce())
            ->method('getManagerForClass')
            ->will($this->returnValue($this->getEntityManager()));

        return new DoctrineTypeDriver(
            $this->getAnnotationDriver(),
            $registry
        );
    }
}
