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

namespace JMS\SerializerBundle\Tests\Serializer;

use Metadata\MetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\Tests\Fixtures\InvalidUsageOfXmlValue;
use JMS\SerializerBundle\Serializer\Construction\UnserializeObjectConstructor;
use JMS\SerializerBundle\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\SerializerBundle\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\SerializerBundle\Serializer\XmlDeserializationVisitor;
use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use JMS\SerializerBundle\Serializer\Serializer;
use JMS\SerializerBundle\Exception\InvalidArgumentException;
use JMS\SerializerBundle\Tests\Fixtures\PersonCollection;
use JMS\SerializerBundle\Tests\Fixtures\PersonLocation;
use JMS\SerializerBundle\Tests\Fixtures\Person;
use JMS\SerializerBundle\Tests\Fixtures\ObjectWithVirtualXmlProperties;
use JMS\SerializerBundle\Tests\Fixtures\ObjectWithXmlKeyValuePairs;

class XmlSerializationTest extends BaseSerializationTest
{
    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidUsageOfXmlValue()
    {
        $obj = new InvalidUsageOfXmlValue();
        $this->serialize($obj);
    }

    public function testPropertyIsObjectWithAttributeAndValue()
    {
        $personCollection = new PersonLocation;
        $person = new Person;
        $person->name = 'Matthias Noback';
        $person->age = 28;
        $personCollection->person = $person;
        $personCollection->location = 'The Netherlands';

        $this->assertEquals($this->getContent('person_location'), $this->serialize($personCollection));
    }

    public function testPropertyIsCollectionOfObjectsWithAttributeAndValue()
    {
        $personCollection = new PersonCollection;
        $person = new Person;
        $person->name = 'Matthias Noback';
        $person->age = 28;
        $personCollection->persons->add($person);
        $personCollection->location = 'The Netherlands';

        $this->assertEquals($this->getContent('person_collection'), $this->serialize($personCollection));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Document types are not allowed
     */
    public function testExternalEntitiesAreDisabledByDefault()
    {
        $this->deserialize('<?xml version="1.0"?>
            <!DOCTYPE author [
                <!ENTITY foo SYSTEM "php://filter/read=convert.base64-encode/resource='.basename(__FILE__).'">
            ]>
            <result>
                &foo;
            </result>', 'stdClass');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Document types are not allowed
     */
    public function testDocumentTypesAreNotAllowed()
    {
        $this->deserialize('<?xml version="1.0"?><!DOCTYPE foo><foo></foo>', 'stdClass');
    }

    public function testWhitelistedDocumentTypesAreAllowed()
    {
        $xmlVisitor = new XmlDeserializationVisitor(
            new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy()),
            $this->getDeserializationHandlers(),
            new UnserializeObjectConstructor()
        );
        $xmlVisitor->setDocumentWhitelist(array(
            '<!DOCTYPE authorized SYSTEM "http://authorized_url.dtd">',
            '<!DOCTYPE author [<!ENTITY foo SYSTEM "php://filter/read=convert.base64-encode/resource='.basename(__FILE__).'">]>'));

        $serializer = new Serializer(new MetadataFactory(new AnnotationDriver(new AnnotationReader())), array(), array('xml' => $xmlVisitor));

        $serializer->deserialize('<?xml version="1.0"?>
            <!DOCTYPE authorized SYSTEM "http://authorized_url.dtd">
            <foo></foo>', 'stdClass', 'xml');

        $serializer->deserialize('<?xml version="1.0"?>
            <!DOCTYPE author [
                <!ENTITY foo SYSTEM "php://filter/read=convert.base64-encode/resource='.basename(__FILE__).'">
            ]>
            <foo></foo>', 'stdClass', 'xml');
    }

    public function testVirtualAttributes()
    {
        $serializer = $this->getSerializer();
        $serializer->setGroups(array('attributes'));
        $this->assertEquals($this->getContent('virtual_attributes'), $serializer->serialize(new ObjectWithVirtualXmlProperties(),'xml'));
    }

    public function testVirtualValues()
    {
        $serializer = $this->getSerializer();
        $serializer->setGroups(array('values'));
        $this->assertEquals($this->getContent('virtual_values'), $serializer->serialize(new ObjectWithVirtualXmlProperties(),'xml'));
    }

    public function testVirtualXmlList()
    {
        $serializer = $this->getSerializer();
        $serializer->setGroups(array('list'));
        $this->assertEquals($this->getContent('virtual_properties_list'), $serializer->serialize(new ObjectWithVirtualXmlProperties(),'xml'));
    }

    public function testVirtualXmlMap()
    {
        $serializer = $this->getSerializer();
        $serializer->setGroups(array('map'));
        $this->assertEquals($this->getContent('virtual_properties_map'), $serializer->serialize(new ObjectWithVirtualXmlProperties(),'xml'));
    }

    public function testArrayKeyValues()
    {
        $serializer = $this->getSerializer();
        $this->assertEquals($this->getContent('array_key_values'), $serializer->serialize(new ObjectWithXmlKeyValuePairs(), 'xml'));
    }

    /**
     * @param string $key
     */
    protected function getContent($key)
    {
        if (!file_exists($file = __DIR__.'/xml/'.$key.'.xml')) {
            throw new InvalidArgumentException(sprintf('The key "%s" is not supported.', $key));
        }

        return file_get_contents($file);
    }

    protected function getFormat()
    {
        return 'xml';
    }
}
