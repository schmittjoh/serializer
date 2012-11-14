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

use JMS\SerializerBundle\Tests\Fixtures\InvalidUsageOfXmlValue;
use JMS\SerializerBundle\Serializer\Serializer;
use JMS\SerializerBundle\Exception\InvalidArgumentException;
use JMS\SerializerBundle\Tests\Fixtures\PersonCollection;
use JMS\SerializerBundle\Tests\Fixtures\PersonLocation;
use JMS\SerializerBundle\Tests\Fixtures\Person;
use JMS\SerializerBundle\Tests\Fixtures\ObjectWithVirtualXmlProperties;
use JMS\SerializerBundle\Tests\Fixtures\ObjectWithXmlKeyValuePairs;
use JMS\SerializerBundle\Tests\Fixtures\Input;

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
     * @expectedExceptionMessage The document type "<!DOCTYPE author [<!ENTITY foo SYSTEM "php://filter/read=convert.base64-encode/resource=XmlSerializationTest.php">]>" is not allowed. If it is safe, you may add it to the whitelist configuration.
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
     * @expectedExceptionMessage The document type "<!DOCTYPE foo>" is not allowed. If it is safe, you may add it to the whitelist configuration.
     */
    public function testDocumentTypesAreNotAllowed()
    {
        $this->deserialize('<?xml version="1.0"?><!DOCTYPE foo><foo></foo>', 'stdClass');
    }

    public function testWhitelistedDocumentTypesAreAllowed()
    {
        $this->deserializationVisitors['xml']->setDoctypeWhitelist(array(
            '<!DOCTYPE authorized SYSTEM "http://authorized_url.dtd">',
            '<!DOCTYPE author [<!ENTITY foo SYSTEM "php://filter/read=convert.base64-encode/resource='.basename(__FILE__).'">]>'));

        $this->serializer->deserialize('<?xml version="1.0"?>
            <!DOCTYPE authorized SYSTEM "http://authorized_url.dtd">
            <foo></foo>', 'stdClass', 'xml');

        $this->serializer->deserialize('<?xml version="1.0"?>
            <!DOCTYPE author [
                <!ENTITY foo SYSTEM "php://filter/read=convert.base64-encode/resource='.basename(__FILE__).'">
            ]>
            <foo></foo>', 'stdClass', 'xml');
    }

    public function testVirtualAttributes()
    {
        $this->serializer->setGroups(array('attributes'));
        $this->assertEquals($this->getContent('virtual_attributes'), $this->serializer->serialize(new ObjectWithVirtualXmlProperties(),'xml'));
    }

    public function testVirtualValues()
    {
        $this->serializer->setGroups(array('values'));
        $this->assertEquals($this->getContent('virtual_values'), $this->serializer->serialize(new ObjectWithVirtualXmlProperties(),'xml'));
    }

    public function testVirtualXmlList()
    {
        $this->serializer->setGroups(array('list'));
        $this->assertEquals($this->getContent('virtual_properties_list'), $this->serializer->serialize(new ObjectWithVirtualXmlProperties(),'xml'));
    }

    public function testVirtualXmlMap()
    {
        $this->serializer->setGroups(array('map'));
        $this->assertEquals($this->getContent('virtual_properties_map'), $this->serializer->serialize(new ObjectWithVirtualXmlProperties(),'xml'));
    }

    public function testArrayKeyValues()
    {
        $this->assertEquals($this->getContent('array_key_values'), $this->serializer->serialize(new ObjectWithXmlKeyValuePairs(), 'xml'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unsupported value type for XML attribute map. Expected array but got object
     */
    public function testXmlAttributeMapWithoutArray()
    {
        $attributes = new \ArrayObject(array(
            'type' => 'text',
        ));

        $this->serializer->serialize(new Input($attributes), $this->getFormat());
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
