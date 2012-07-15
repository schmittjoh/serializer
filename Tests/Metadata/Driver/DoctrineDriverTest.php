<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Tests\Metadata\Driver;

use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use JMS\SerializerBundle\Metadata\Driver\DoctrineTypeDriver;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;

class DoctrineDriverTest extends \PHPUnit_Framework_TestCase
{
    public function getMetadata()
    {
        $refClass = new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\Doctrine\BlogPost');
        $metadata = $this->getDoctrineDriver()->loadMetadataForClass($refClass);

        return array(array($metadata));
    }

    /**
     * @dataProvider getMetadata
     */
    public function testTypelessPropertyIsGivenTypeFromDoctrineMetadata($metadata)
    {
        $this->assertEquals('DateTime', $metadata->propertyMetadata['createdAt']->type);
    }

    /**
     * @dataProvider getMetadata
     */
    public function testSingleValuedAssociationIsProperlyHinted($metadata)
    {
        $this->assertEquals('JMS\SerializerBundle\Tests\Fixtures\Doctrine\Author', $metadata->propertyMetadata['author']->type);
    }

    /**
     * @dataProvider getMetadata
     */
    public function testMultiValuedAssociationIsProperlyHinted($metadata)
    {
        $this->assertEquals('ArrayCollection<JMS\SerializerBundle\Tests\Fixtures\Doctrine\Comment>', $metadata->propertyMetadata['comments']->type);
    }
    
    /**
     * @dataProvider getMetadata
     */
    public function testTypeGuessByDoctrineIsOverwrittenByDelegateDriver($metadata)
    {
        // This would be guessed as boolean but we've overriden it to integer
        $this->assertEquals('integer', $metadata->propertyMetadata['published']->type);
    }

    /**
     * @dataProvider getMetadata
     */
    public function testUnknownDoctrineTypeDoesNotResultInAGuess($metadata)
    {
        $this->assertNull($metadata->propertyMetadata['slug']->type);
    }

    public function testNonDoctrineEntityClassIsNotModified()
    {
        // Note: Using regular BlogPost fixture here instead of Doctrine fixture
        // because it has no Doctrine metadata.
        $refClass = new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\BlogPost');

        $plainMetadata = $this->getAnnotationDriver()->loadMetadataForClass($refClass);
        $doctrineMetadata = $this->getDoctrineDriver()->loadMetadataForClass($refClass);

        $this->assertEquals($plainMetadata, $doctrineMetadata);        
    }
    
    protected function getEntityManager()
    {
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir() . '/JMSDoctrineTestProxies');
        $config->setProxyNamespace('JMS\Tests\Proxies');
        $config->setMetadataDriverImpl(
            new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(new AnnotationReader(), __DIR__.'/../../Fixtures/Doctrine')
        );

        $conn = array(
            'driver'    => 'pdo_sqlite',
            'path'      => sys_get_temp_dir() . '/jms_test_database.sqlite',
        );

        return EntityManager::create($conn, $config);
    }

    public function getAnnotationDriver()
    {
        return new AnnotationDriver(new AnnotationReader());
    }

    protected function getDoctrineDriver()
    {
        return new DoctrineTypeDriver(
            $this->getAnnotationDriver(),
            $this->getEntityManager()
        );
    }
}
