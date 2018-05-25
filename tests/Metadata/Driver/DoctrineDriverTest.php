<?php

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver as DoctrineDriver;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\DoctrineTypeDriver;

class DoctrineDriverTest extends \PHPUnit_Framework_TestCase
{
    public function getMetadata()
    {
        $refClass = new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Doctrine\BlogPost');
        $metadata = $this->getDoctrineDriver()->loadMetadataForClass($refClass);

        return $metadata;
    }

    public function testTypelessPropertyIsGivenTypeFromDoctrineMetadata()
    {
        $metadata = $this->getMetadata();

        $this->assertEquals(
            array('name' => 'DateTime', 'params' => array()),
            $metadata->propertyMetadata['createdAt']->type
        );
    }

    public function testSingleValuedAssociationIsProperlyHinted()
    {
        $metadata = $this->getMetadata();
        $this->assertEquals(
            array('name' => 'JMS\Serializer\Tests\Fixtures\Doctrine\Author', 'params' => array()),
            $metadata->propertyMetadata['author']->type
        );
    }

    public function testMultiValuedAssociationIsProperlyHinted()
    {
        $metadata = $this->getMetadata();

        $this->assertEquals(
            array('name' => 'ArrayCollection', 'params' => array(
                array('name' => 'JMS\Serializer\Tests\Fixtures\Doctrine\Comment', 'params' => array()))
            ),
            $metadata->propertyMetadata['comments']->type
        );
    }

    public function testTypeGuessByDoctrineIsOverwrittenByDelegateDriver()
    {
        $metadata = $this->getMetadata();

        // This would be guessed as boolean but we've overriden it to integer
        $this->assertEquals(
            array('name' => 'integer', 'params' => array()),
            $metadata->propertyMetadata['published']->type
        );
    }

    public function testUnknownDoctrineTypeDoesNotResultInAGuess()
    {
        $metadata = $this->getMetadata();
        $this->assertNull($metadata->propertyMetadata['slug']->type);
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

        $this->assertEquals($plainMetadata, $doctrineMetadata);
    }

    public function testExcludePropertyNoPublicAccessorException()
    {
        $first = $this->getAnnotationDriver()
            ->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\ExcludePublicAccessor'));

        $this->assertArrayHasKey('id', $first->propertyMetadata);
        $this->assertArrayNotHasKey('iShallNotBeAccessed', $first->propertyMetadata);
    }

    public function testVirtualPropertiesAreNotModified()
    {
        $doctrineMetadata = $this->getMetadata();
        $this->assertNull($doctrineMetadata->propertyMetadata['ref']->type);
    }

    public function testGuidPropertyIsGivenStringType()
    {
        $metadata = $this->getMetadata();

        $this->assertEquals(
            array('name' => 'string', 'params' => array()),
            $metadata->propertyMetadata['id']->type
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

        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        return EntityManager::create($conn, $config);
    }

    public function getAnnotationDriver()
    {
        return new AnnotationDriver(new AnnotationReader());
    }

    protected function getDoctrineDriver()
    {
        $registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')->getMock();
        $registry->expects($this->atLeastOnce())
            ->method('getManagerForClass')
            ->will($this->returnValue($this->getEntityManager()));

        return new DoctrineTypeDriver(
            $this->getAnnotationDriver(),
            $registry
        );
    }
}
