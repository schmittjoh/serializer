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

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\Context;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\Tests\Fixtures\AccessorFinder;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlAttributeDiscriminatorChild;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlAttributeDiscriminatorParent;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceDiscriminatorChild;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceDiscriminatorParent;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNotCDataDiscriminatorChild;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNotCDataDiscriminatorParent;
use JMS\Serializer\Tests\Fixtures\Input;
use JMS\Serializer\Tests\Fixtures\InvalidUsageOfXmlValue;
use JMS\Serializer\Tests\Fixtures\ObjectWithNamespacesAndList;
use JMS\Serializer\Tests\Fixtures\ObjectWithNamespacesAndNestedList;
use JMS\Serializer\Tests\Fixtures\ObjectWithToString;
use JMS\Serializer\Tests\Fixtures\ObjectWithVirtualXmlProperties;
use JMS\Serializer\Tests\Fixtures\ObjectWithXmlKeyValuePairs;
use JMS\Serializer\Tests\Fixtures\ObjectWithXmlKeyValuePairsWithObjectType;
use JMS\Serializer\Tests\Fixtures\ObjectWithXmlKeyValuePairsWithType;
use JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespaces;
use JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespacesAndObjectProperty;
use JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespacesAndObjectPropertyAuthor;
use JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespacesAndObjectPropertyVirtual;
use JMS\Serializer\Tests\Fixtures\ObjectWithXmlRootNamespace;
use JMS\Serializer\Tests\Fixtures\Person;
use JMS\Serializer\Tests\Fixtures\PersonCollection;
use JMS\Serializer\Tests\Fixtures\PersonLocation;
use JMS\Serializer\Tests\Fixtures\SimpleClassObject;
use JMS\Serializer\Tests\Fixtures\SimpleObject;
use JMS\Serializer\Tests\Fixtures\SimpleSubClassObject;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use PhpCollection\Map;

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


    /**
     * @dataProvider getXMLBooleans
     */
    public function testXMLBooleans($xmlBoolean, $boolean)
    {
        if ($this->hasDeserializer()) {
            $this->assertSame($boolean, $this->deserialize('<result>' . $xmlBoolean . '</result>', 'boolean'));
        }
    }

    public function getXMLBooleans()
    {
        return array(array('true', true), array('false', false), array('1', true), array('0', false));
    }

    public function testAccessorFinderDeserialization()
    {
        $value = 2018;
        $finder = $this->deserialize(<<<XML
<?xml version="1.0"?>
<AccessorGuesser>
    <a1_b>$value</a1_b>
</AccessorGuesser>
XML
,
            AccessorFinder::class
        );

        /** @var AccessorFinder $finder */
        $this->assertSame($value, $finder->getA1B());
    }

    public function testAccessorSetterDeserialization()
    {
        /** @var \JMS\Serializer\Tests\Fixtures\AccessorSetter $object */
        $object = $this->deserialize('<?xml version="1.0"?>
            <AccessorSetter>
                <element attribute="attribute">element</element>
                <collection>
                    <entry>collectionEntry</entry>
                </collection>
            </AccessorSetter>',
            'JMS\Serializer\Tests\Fixtures\AccessorSetter'
        );

        $this->assertInstanceOf('stdClass', $object->getElement());
        $this->assertInstanceOf('JMS\Serializer\Tests\Fixtures\AccessorSetterElement', $object->getElement()->element);
        $this->assertEquals('attribute-different', $object->getElement()->element->getAttribute());
        $this->assertEquals('element-different', $object->getElement()->element->getElement());
        $this->assertEquals(['collectionEntry' => 'collectionEntry'], $object->getCollection());
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
                <!ENTITY foo SYSTEM "php://filter/read=convert.base64-encode/resource=' . basename(__FILE__) . '">
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
            '<!DOCTYPE author [<!ENTITY foo SYSTEM "php://filter/read=convert.base64-encode/resource=' . basename(__FILE__) . '">]>'));

        $this->serializer->deserialize('<?xml version="1.0"?>
            <!DOCTYPE authorized SYSTEM "http://authorized_url.dtd">
            <foo></foo>', 'stdClass', 'xml');

        $this->serializer->deserialize('<?xml version="1.0"?>
            <!DOCTYPE author [
                <!ENTITY foo SYSTEM "php://filter/read=convert.base64-encode/resource=' . basename(__FILE__) . '">
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

    public function testUnserializeMissingArray()
    {
        $xml = '<result></result>';
        $object = $this->serializer->deserialize($xml, 'JMS\Serializer\Tests\Fixtures\ObjectWithAbsentXmlListNode', 'xml');
        $this->assertEquals($object->absentAndNs, array());

        $xml = '<result xmlns:x="http://www.example.com">
                    <absent_and_ns>
                        <x:entry>foo</x:entry>
                    </absent_and_ns>
                  </result>';
        $object = $this->serializer->deserialize($xml, 'JMS\Serializer\Tests\Fixtures\ObjectWithAbsentXmlListNode', 'xml');
        $this->assertEquals($object->absentAndNs, array("foo"));
    }

    public function testObjectWithNamespacesAndList()
    {
        $object = new ObjectWithNamespacesAndList();
        $object->name = 'name';
        $object->nameAlternativeB = 'nameB';

        $object->phones = array('111', '222');
        $object->addresses = array('A' => 'Street 1', 'B' => 'Street 2');

        $object->phonesAlternativeB = array('555', '666');
        $object->addressesAlternativeB = array('A' => 'Street 5', 'B' => 'Street 6');

        $object->phonesAlternativeC = array('777', '888');
        $object->addressesAlternativeC = array('A' => 'Street 7', 'B' => 'Street 8');

        $object->phonesAlternativeD = array('999', 'AAA');
        $object->addressesAlternativeD = array('A' => 'Street 9', 'B' => 'Street A');

        $this->assertEquals(
            $this->getContent('object_with_namespaces_and_list'),
            $this->serialize($object, SerializationContext::create())
        );
        $this->assertEquals(
            $object,
            $this->deserialize($this->getContent('object_with_namespaces_and_list'), get_class($object))
        );
    }

    public function testObjectWithNamespaceAndNestedList()
    {
        $object = new ObjectWithNamespacesAndNestedList();
        $personCollection = new PersonCollection();
        $personA = new Person();
        $personA->age = 11;
        $personA->name = 'AAA';

        $personB = new Person();
        $personB->age = 22;
        $personB->name = 'BBB';

        $personCollection->persons->add($personA);
        $personCollection->persons->add($personB);

        $object->personCollection = $personCollection;

        $this->assertEquals(
            $this->getContent('object_with_namespaces_and_nested_list'),
            $this->serialize($object, SerializationContext::create())
        );
        $this->assertEquals(
            $object,
            $this->deserialize($this->getContent('object_with_namespaces_and_nested_list'), get_class($object))
        );
    }

    public function testArrayKeyValues()
    {
        $this->assertEquals($this->getContent('array_key_values'), $this->serializer->serialize(new ObjectWithXmlKeyValuePairs(), 'xml'));
    }

    public function testDeserializeArrayKeyValues()
    {
        $xml = $this->getContent('array_key_values_with_type_1');
        $result = $this->serializer->deserialize($xml, ObjectWithXmlKeyValuePairsWithType::class, 'xml');

        $this->assertInstanceOf(ObjectWithXmlKeyValuePairsWithType::class, $result);
        $this->assertEquals(ObjectWithXmlKeyValuePairsWithType::create1(), $result);

        $xml2 = $this->getContent('array_key_values_with_type_2');
        $result2 = $this->serializer->deserialize($xml2, ObjectWithXmlKeyValuePairsWithType::class, 'xml');

        $this->assertInstanceOf(ObjectWithXmlKeyValuePairsWithType::class, $result2);
        $this->assertEquals(ObjectWithXmlKeyValuePairsWithType::create2(), $result2);
    }

    public function testDeserializeTypedAndNestedArrayKeyValues()
    {
        $xml = $this->getContent('array_key_values_with_nested_type');
        $result = $this->serializer->deserialize($xml, ObjectWithXmlKeyValuePairsWithObjectType::class, 'xml');

        $this->assertInstanceOf(ObjectWithXmlKeyValuePairsWithObjectType::class, $result);
        $this->assertEquals(ObjectWithXmlKeyValuePairsWithObjectType::create1(), $result);
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
     * @dataProvider getDateTimeImmutable
     * @group datetime
     */
    public function testDateTimeImmutableNoCData($key, $value, $type)
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

    public function testObjectWithOnlyNamespacesAndList()
    {
        $object = new ObjectWithNamespacesAndList();

        $object->phones = array();
        $object->addresses = array();

        $object->phonesAlternativeB = array();
        $object->addressesAlternativeB = array();

        $object->phonesAlternativeC = array('777', '888');
        $object->addressesAlternativeC = array('A' => 'Street 7', 'B' => 'Street 8');

        $object->phonesAlternativeD = array();
        $object->addressesAlternativeD = array();

        $this->assertEquals(
            $this->getContent('object_with_only_namespaces_and_list'),
            $this->serialize($object, SerializationContext::create())
        );

        $deserialized = $this->deserialize($this->getContent('object_with_only_namespaces_and_list'), get_class($object));
        $this->assertEquals($object, $deserialized);
    }

    public function testDeserializingNull()
    {
        $this->markTestSkipped('Not supported in XML.');
    }

    public function testDeserializeWithObjectWithToStringMethod()
    {
        $input = new ObjectWithToString($this->getContent('simple_object'));

        $object = $this->deserialize($input, SimpleObject::class);

        $this->assertInstanceOf(SimpleObject::class, $object);
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

    public function testObjectWithXmlNamespacesAndBackReferencedNamespaces()
    {
        $author = new ObjectWithXmlNamespacesAndObjectPropertyAuthor('mr', 'smith');
        $object = new ObjectWithXmlNamespacesAndObjectProperty('This is a nice title.', $author);

        $serialized = $this->serialize($object);
        $this->assertEquals($this->getContent('object_with_xml_namespaces_and_object_property'), $serialized);
    }

    public function testObjectWithXmlNamespacesAndBackReferencedNamespacesWithListeners()
    {
        $author = new ObjectWithXmlNamespacesAndObjectPropertyAuthor('mr', 'smith');
        $object = new ObjectWithXmlNamespacesAndObjectPropertyVirtual('This is a nice title.', new \stdClass());

        $this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_SERIALIZATION, 'ObjectWithXmlNamespacesAndObjectPropertyAuthorVirtual', $this->getFormat(),
            function (XmlSerializationVisitor $visitor, $data, $type, Context $context) use ($author) {
                $factory = $context->getMetadataFactory(get_class($author));
                $classMetadata = $factory->getMetadataForClass(get_class($author));

                $metadata = new StaticPropertyMetadata(get_class($author), 'foo', $author);
                $metadata->xmlNamespace = $classMetadata->xmlRootNamespace;
                $metadata->xmlNamespace = $classMetadata->xmlRootNamespace;

                $visitor->visitProperty($metadata, $author, $context);
            }
        );

        $serialized = $this->serialize($object);
        $this->assertEquals($this->getContent('object_with_xml_namespaces_and_object_property_virtual'), $serialized);
    }

    public function testObjectWithXmlRootNamespace()
    {
        $object = new ObjectWithXmlRootNamespace('This is a nice title.', 'Foo Bar', new \DateTime('2011-07-30 00:00', new \DateTimeZone('UTC')), 'en');
        $this->assertEquals($this->getContent('object_with_xml_root_namespace'), $this->serialize($object));
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

    public function testWithoutFormatedOutputByXmlSerializationVisitor()
    {
        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        $xmlVisitor = new XmlSerializationVisitor($namingStrategy);
        $xmlVisitor->setFormatOutput(false);

        $visitors = new Map(array(
            'xml' => new XmlSerializationVisitor($namingStrategy),
        ));

        $serializer = new Serializer(
            $this->factory,
            $this->handlerRegistry,
            new UnserializeObjectConstructor(),
            $visitors,
            $this->deserializationVisitors,
            $this->dispatcher
        );

        $object = new SimpleClassObject;
        $object->foo = 'foo';
        $object->bar = 'bar';
        $object->moo = 'moo';

        $stringXml = $serializer->serialize($object, $this->getFormat());
        $this->assertXmlStringEqualsXmlString($this->getContent('simple_class_object_minified'), $stringXml);
    }

    public function testDiscriminatorAsXmlAttribute()
    {
        $xml = $this->serialize(new ObjectWithXmlAttributeDiscriminatorChild());
        $this->assertEquals($this->getContent('xml_discriminator_attribute'), $xml);
        $this->assertInstanceOf(
            ObjectWithXmlAttributeDiscriminatorChild::class,
            $this->deserialize(
                $xml,
                ObjectWithXmlAttributeDiscriminatorParent::class
            )
        );
    }

    public function testDiscriminatorAsNotCData()
    {
        $xml = $this->serialize(new ObjectWithXmlNotCDataDiscriminatorChild());
        $this->assertEquals($this->getContent('xml_discriminator_not_cdata'), $xml);
        $this->assertInstanceOf(
            ObjectWithXmlNotCDataDiscriminatorChild::class,
            $this->deserialize(
                $xml,
                ObjectWithXmlNotCDataDiscriminatorParent::class
            )
        );
    }

    public function testDiscriminatorWithNamespace()
    {
        $xml = $this->serialize(new ObjectWithXmlNamespaceDiscriminatorChild());
        $this->assertEquals($this->getContent('xml_discriminator_namespace'), $xml);

        $this->assertInstanceOf(
            ObjectWithXmlNamespaceDiscriminatorChild::class,
            $this->deserialize(
                $xml,
                ObjectWithXmlNamespaceDiscriminatorParent::class
            )
        );
    }

    /**
     * @expectedException \JMS\Serializer\Exception\XmlErrorException
     */
    public function testDeserializeEmptyString()
    {
        $this->deserialize('', 'stdClass');
    }

    public function testEvaluatesToNull()
    {
        $namingStrategy = $this->getMockBuilder(PropertyNamingStrategyInterface::class)->getMock();
        $visitor = new XmlDeserializationVisitor($namingStrategy);
        $xsdNilAsTrueElement = simplexml_load_string('<empty xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="true"/>');
        $xsdNilAsOneElement = simplexml_load_string('<empty xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/>');

        $this->assertTrue($visitor->isNull($xsdNilAsTrueElement));
        $this->assertTrue($visitor->isNull($xsdNilAsOneElement));
        $this->assertTrue($visitor->isNull(null));
    }

    private function xpathFirstToString(\SimpleXMLElement $xml, $xpath)
    {
        $nodes = $xml->xpath($xpath);
        return (string)reset($nodes);
    }

    /**
     * @param string $key
     */
    protected function getContent($key)
    {
        if (!file_exists($file = __DIR__ . '/xml/' . $key . '.xml')) {
            throw new InvalidArgumentException(sprintf('The key "%s" is not supported.', $key));
        }

        return file_get_contents($file);
    }

    protected function getFormat()
    {
        return 'xml';
    }
}
