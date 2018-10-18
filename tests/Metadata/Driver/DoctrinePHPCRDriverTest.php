<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\PHPCR\Configuration;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Mapping\Driver\AnnotationDriver as DoctrinePHPCRDriver;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\DoctrinePHPCRTypeDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use PHPUnit\Framework\TestCase;

class DoctrinePHPCRDriverTest extends TestCase
{
    public function getMetadata()
    {
        $refClass = new \ReflectionClass('JMS\Serializer\Tests\Fixtures\DoctrinePHPCR\BlogPost');
        return $this->getDoctrinePHPCRDriver()->loadMetadataForClass($refClass);
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
            ['name' => 'JMS\Serializer\Tests\Fixtures\DoctrinePHPCR\Author', 'params' => []],
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
                    ['name' => 'JMS\Serializer\Tests\Fixtures\DoctrinePHPCR\Comment', 'params' => []],
                ],
            ],
            $metadata->propertyMetadata['comments']->type
        );
    }

    public function testTypeGuessByDoctrineIsOverwrittenByDelegateDriver()
    {
        $metadata = $this->getMetadata();

        // This would be guessed as boolean but we've overridden it to integer
        self::assertEquals(
            ['name' => 'integer', 'params' => []],
            $metadata->propertyMetadata['published']->type
        );
    }

    public function testNonDoctrineDocumentClassIsNotModified()
    {
        // Note: Using regular BlogPost fixture here instead of Doctrine fixture
        // because it has no Doctrine metadata.
        $refClass = new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost');

        $plainMetadata = $this->getAnnotationDriver()->loadMetadataForClass($refClass);
        $doctrineMetadata = $this->getDoctrinePHPCRDriver()->loadMetadataForClass($refClass);

        // Do not compare timestamps
        if (abs($doctrineMetadata->createdAt - $plainMetadata->createdAt) < 2) {
            $plainMetadata->createdAt = $doctrineMetadata->createdAt;
        }

        self::assertEquals($plainMetadata, $doctrineMetadata);
    }

    protected function getDocumentManager()
    {
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir() . '/JMSDoctrineTestProxies');
        $config->setProxyNamespace('JMS\Tests\Proxies');
        $config->setMetadataDriverImpl(
            new DoctrinePHPCRDriver(new AnnotationReader(), __DIR__ . '/../../Fixtures/DoctrinePHPCR')
        );

        $session = $this->getMockBuilder('PHPCR\SessionInterface')->getMock();

        return DocumentManager::create($session, $config);
    }

    public function getAnnotationDriver()
    {
        return new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy());
    }

    protected function getDoctrinePHPCRDriver()
    {
        $registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')->getMock();
        $registry->expects($this->atLeastOnce())
            ->method('getManagerForClass')
            ->will($this->returnValue($this->getDocumentManager()));

        return new DoctrinePHPCRTypeDriver(
            $this->getAnnotationDriver(),
            $registry
        );
    }
}
