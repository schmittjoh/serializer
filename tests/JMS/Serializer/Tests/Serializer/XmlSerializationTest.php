<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\Tests\Fixtures\InvalidUsageOfXmlValue;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Tests\Fixtures\PersonCollection;
use JMS\Serializer\Tests\Fixtures\PersonLocation;
use JMS\Serializer\Tests\Fixtures\Person;
use JMS\Serializer\Tests\Fixtures\ObjectWithVirtualXmlProperties;
use JMS\Serializer\Tests\Fixtures\ObjectWithXmlKeyValuePairs;
use JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespaces;
use JMS\Serializer\Tests\Fixtures\Input;
use JMS\Serializer\Tests\Fixtures\SimpleClassObject;
use JMS\Serializer\Tests\Fixtures\SimpleSubClassObject;

class XmlSerializationTest extends BaseSerializationTest
{
    /**
     * @expectedException JMS\Serializer\Exception\RuntimeException
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
     * @expectedException JMS\Serializer\Exception\InvalidArgumentException
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
     * @expectedException JMS\Serializer\Exception\InvalidArgumentException
     * @expectedExceptionMessage The document type "<!DOCTYPE foo>" is not allowed. If it is safe, you may add it to the whitelist configuration.
     */
    public function testDocumentTypesAreNotAllowed()
    {
        $this->deserialize('<?xml version="1.0"?><!DOCTYPE foo><foo></foo>', 'stdClass');
    }

    public function testWhitelistedDocumentTypesAreAllowed()
    {
        $this->deserializationVisitors->get('xml')->get()->setDoctypeWhitelist(array(
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
        $this->assertEquals(
            $this->getContent('virtual_attributes'),
            $this->serialize(new ObjectWithVirtualXmlProperties(), SerializationContext::create()->setGroups(array('attributes')))
        );
    }

    public function testVirtualValues()
    {
        $this->assertEquals(
            $this->getContent('virtual_values'),
            $this->serialize(new ObjectWithVirtualXmlProperties(), SerializationContext::create()->setGroups(array('values')))
        );
    }

    public function testVirtualXmlList()
    {
        $this->assertEquals(
            $this->getContent('virtual_properties_list'),
            $this->serialize(new ObjectWithVirtualXmlProperties(), SerializationContext::create()->setGroups(array('list')))
        );
    }

    public function testVirtualXmlMap()
    {
        $this->assertEquals(
            $this->getContent('virtual_properties_map'),
            $this->serialize(new ObjectWithVirtualXmlProperties(), SerializationContext::create()->setGroups(array('map')))
        );
    }

    public function testArrayKeyValues()
    {
        $this->assertEquals($this->getContent('array_key_values'), $this->serializer->serialize(new ObjectWithXmlKeyValuePairs(), 'xml'));
    }

    /**
     * @dataProvider getDateTime
     * @group datetime
     */
    public function testDateTimeNoCData($key, $value, $type)
    {
        $handlerRegistry = new HandlerRegistry();
        $handlerRegistry->registerSubscribingHandler(new DateHandler(\DateTime::ISO8601, 'UTC', false));
        $objectConstructor = new UnserializeObjectConstructor();

        $serializer = new Serializer($this->factory, $handlerRegistry, $objectConstructor, $this->serializationVisitors, $this->deserializationVisitors);

        $this->assertEquals($this->getContent($key . '_no_cdata'), $serializer->serialize($value, $this->getFormat()));
    }

    /**
     * @expectedException JMS\Serializer\Exception\RuntimeException
     * @expectedExceptionMessage Unsupported value type for XML attribute map. Expected array but got object
     */
    public function testXmlAttributeMapWithoutArray()
    {
        $attributes = new \ArrayObject(array(
            'type' => 'text',
        ));

        $this->serializer->serialize(new Input($attributes), $this->getFormat());
    }

    public function testDeserializingNull()
    {
        $this->markTestSkipped('Not supported in XML.');
    }

    public function testObjectWithXmlNamespaces()
    {
        $object = new ObjectWithXmlNamespaces('This is a nice title.', 'Foo Bar', new \DateTime('2011-07-30 00:00', new \DateTimeZone('UTC')), 'en');

        $serialized = $this->serialize($object);
        $this->assertEquals($this->getContent('object_with_xml_namespaces'), $this->serialize($object));

        $xml = simplexml_load_string($this->serialize($object));
        $xml->registerXPathNamespace('ns1', "http://purl.org/dc/elements/1.1/");
        $xml->registerXPathNamespace('ns2', "http://schemas.google.com/g/2005");
        $xml->registerXPathNamespace('ns3', "http://www.w3.org/2005/Atom");

        $this->assertEquals('2011-07-30T00:00:00+0000', $this->xpathFirstToString($xml, './@created_at'));
        $this->assertEquals('1edf9bf60a32d89afbb85b2be849e3ceed5f5b10', $this->xpathFirstToString($xml, './@ns2:etag'));
        $this->assertEquals('en', $this->xpathFirstToString($xml, './@ns1:language'));
        $this->assertEquals('This is a nice title.', $this->xpathFirstToString($xml, './ns1:title'));
        $this->assertEquals('Foo Bar', $this->xpathFirstToString($xml, './ns3:author'));

        $deserialized = $this->deserialize($this->getContent('object_with_xml_namespacesalias'), get_class($object));
        $this->assertEquals('2011-07-30T00:00:00+0000', $this->getField($deserialized, 'createdAt')->format(\DateTime::ISO8601));
        $this->assertAttributeEquals('This is a nice title.', 'title', $deserialized);
        $this->assertAttributeSame('1edf9bf60a32d89afbb85b2be849e3ceed5f5b10', 'etag', $deserialized);
        $this->assertAttributeSame('en', 'language', $deserialized);
        $this->assertAttributeEquals('Foo Bar', 'author', $deserialized);

    }

    public function testXmlNamespacesInheritance()
    {
        $object = new SimpleClassObject();
        $object->foo = 'foo';
        $object->bar = 'bar';
        $object->moo = 'moo';

        $this->assertEquals($this->getContent('simple_class_object'), $this->serialize($object));

        $childObject = new SimpleSubClassObject();
        $childObject->foo = 'foo';
        $childObject->bar = 'bar';
        $childObject->moo = 'moo';
        $childObject->baz = 'baz';
        $childObject->qux = 'qux';


        $this->assertEquals($this->getContent('simple_subclass_object'), $this->serialize($childObject));
    }

    private function xpathFirstToString(\SimpleXMLElement $xml, $xpath)
    {
        $nodes = $xml->xpath($xpath);
        return (string) reset($nodes);
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
