<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\DoctrinePHPCRTypeDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\PHPCR\Configuration;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Mapping\Driver\AnnotationDriver as DoctrinePHPCRDriver;

class DoctrinePHPCRDriverTest extends \PHPUnit_Framework_TestCase
{
    public function getMetadata()
    {
        $refClass = new \ReflectionClass('JMS\Serializer\Tests\Fixtures\DoctrinePHPCR\BlogPost');
        $metadata = $this->getDoctrinePHPCRDriver()->loadMetadataForClass($refClass);

        return $metadata;
    }

    public function testTypelessPropertyIsGivenTypeFromDoctrineMetadata()
    {
        $metadata = $this->getMetadata();
        $this->assertEquals(
            array('name'=> 'DateTime', 'params' => array()),
            $metadata->propertyMetadata['createdAt']->type
        );
    }

    public function testSingleValuedAssociationIsProperlyHinted()
    {
        $metadata = $this->getMetadata();
        $this->assertEquals(
            array('name'=> 'JMS\Serializer\Tests\Fixtures\DoctrinePHPCR\Author', 'params' => array()),
            $metadata->propertyMetadata['author']->type
        );
    }

    public function testMultiValuedAssociationIsProperlyHinted()
    {
        $metadata = $this->getMetadata();

        $this->assertEquals(
            array('name'=> 'ArrayCollection', 'params' => array(
                array('name' => 'JMS\Serializer\Tests\Fixtures\DoctrinePHPCR\Comment', 'params' => array()))
            ),
            $metadata->propertyMetadata['comments']->type
        );
    }

    public function testTypeGuessByDoctrineIsOverwrittenByDelegateDriver()
    {
        $metadata = $this->getMetadata();

        // This would be guessed as boolean but we've overridden it to integer
        $this->assertEquals(
            array('name'=> 'integer', 'params' => array()),
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

        $this->assertEquals($plainMetadata, $doctrineMetadata);
    }

    protected function getDocumentManager()
    {
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir() . '/JMSDoctrineTestProxies');
        $config->setProxyNamespace('JMS\Tests\Proxies');
        $config->setMetadataDriverImpl(
            new DoctrinePHPCRDriver(new AnnotationReader(), __DIR__.'/../../Fixtures/DoctrinePHPCR')
        );

        $session = $this->getMock('PHPCR\SessionInterface');

        return DocumentManager::create($session, $config);
    }

    public function getAnnotationDriver()
    {
        return new AnnotationDriver(new AnnotationReader());
    }

    protected function getDoctrinePHPCRDriver()
    {
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->atLeastOnce())
             ->method('getManagerForClass')
             ->will($this->returnValue($this->getDocumentManager()));

        return new DoctrinePHPCRTypeDriver(
            $this->getAnnotationDriver(),
            $registry
        );
    }
}
