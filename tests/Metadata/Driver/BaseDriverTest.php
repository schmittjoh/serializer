<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Exception\InvalidMetadataException;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlAttributeDiscriminatorChild;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlAttributeDiscriminatorParent;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceAttributeDiscriminatorChild;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceAttributeDiscriminatorParent;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceDiscriminatorChild;
use JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceDiscriminatorParent;
use JMS\Serializer\Tests\Fixtures\FirstClassListCollection;
use JMS\Serializer\Tests\Fixtures\FirstClassMapCollection;
use JMS\Serializer\Tests\Fixtures\ObjectWithExpressionVirtualPropertiesAndExcludeAll;
use JMS\Serializer\Tests\Fixtures\ObjectWithInvalidExpression;
use JMS\Serializer\Tests\Fixtures\ObjectWithVirtualPropertiesAndDuplicatePropName;
use JMS\Serializer\Tests\Fixtures\ObjectWithVirtualPropertiesAndExcludeAll;
use JMS\Serializer\Tests\Fixtures\ParentSkipWithEmptyChild;
use Metadata\Driver\DriverInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

abstract class BaseDriverTest extends TestCase
{
    public function testLoadBlogPostMetadata()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\BlogPost'));

        self::assertNotNull($m);
        self::assertEquals('blog-post', $m->xmlRootName);
        self::assertCount(4, $m->xmlNamespaces);
        self::assertArrayHasKey('', $m->xmlNamespaces);
        self::assertEquals('http://example.com/namespace', $m->xmlNamespaces['']);
        self::assertArrayHasKey('gd', $m->xmlNamespaces);
        self::assertEquals('http://schemas.google.com/g/2005', $m->xmlNamespaces['gd']);
        self::assertArrayHasKey('atom', $m->xmlNamespaces);
        self::assertEquals('http://www.w3.org/2005/Atom', $m->xmlNamespaces['atom']);
        self::assertArrayHasKey('dc', $m->xmlNamespaces);
        self::assertEquals('http://purl.org/dc/elements/1.1/', $m->xmlNamespaces['dc']);

        self::assertFalse($m->isList);
        self::assertFalse($m->isMap);

        $p = new PropertyMetadata($m->name, 'id');
        $p->type = ['name' => 'string', 'params' => []];
        $p->groups = ['comments', 'post'];
        $p->serializedName = 'id';
        $p->xmlElementCData = false;
        self::assertEquals($p, $m->propertyMetadata['id']);

        $p = new PropertyMetadata($m->name, 'title');
        $p->type = ['name' => 'string', 'params' => []];
        $p->serializedName = 'title';
        $p->groups = ['comments', 'post'];
        $p->xmlNamespace = 'http://purl.org/dc/elements/1.1/';
        self::assertEquals($p, $m->propertyMetadata['title']);

        $p = new PropertyMetadata($m->name, 'createdAt');
        $p->type = ['name' => 'DateTime', 'params' => []];
        $p->serializedName = 'createdAt';
        $p->xmlAttribute = true;
        self::assertEquals($p, $m->propertyMetadata['createdAt']);

        $p = new PropertyMetadata($m->name, 'published');
        $p->serializedName = 'published';
        $p->type = ['name' => 'boolean', 'params' => []];
        $p->serializedName = 'is_published';
        $p->xmlAttribute = true;
        $p->groups = ['post'];
        self::assertEquals($p, $m->propertyMetadata['published']);

        $p = new PropertyMetadata($m->name, 'etag');
        $p->serializedName = 'etag';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlAttribute = true;
        $p->groups = ['post'];
        $p->xmlNamespace = 'http://schemas.google.com/g/2005';
        self::assertEquals($p, $m->propertyMetadata['etag']);

        $p = new PropertyMetadata($m->name, 'comments');
        $p->serializedName = 'comments';
        $p->type = ['name' => 'ArrayCollection', 'params' => [['name' => 'JMS\Serializer\Tests\Fixtures\Comment', 'params' => []]]];
        $p->xmlCollection = true;
        $p->xmlCollectionInline = true;
        $p->xmlEntryName = 'comment';
        $p->groups = ['comments'];
        self::assertEquals($p, $m->propertyMetadata['comments']);

        $p = new PropertyMetadata($m->name, 'author');
        $p->serializedName = 'author';
        $p->type = ['name' => 'JMS\Serializer\Tests\Fixtures\Author', 'params' => []];
        $p->groups = ['post'];
        $p->xmlNamespace = 'http://www.w3.org/2005/Atom';
        self::assertEquals($p, $m->propertyMetadata['author']);

        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Price'));
        self::assertNotNull($m);

        $p = new PropertyMetadata($m->name, 'price');
        $p->serializedName = 'price';
        $p->type = ['name' => 'float', 'params' => []];
        $p->xmlValue = true;
        self::assertEquals($p, $m->propertyMetadata['price']);
    }

    public function testXMLListAbsentNode()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\ObjectWithAbsentXmlListNode'));

        self::assertArrayHasKey('absent', $m->propertyMetadata);
        self::assertArrayHasKey('present', $m->propertyMetadata);
        self::assertArrayHasKey('skipDefault', $m->propertyMetadata);

        self::assertTrue($m->propertyMetadata['absent']->xmlCollectionSkipWhenEmpty);
        self::assertTrue($m->propertyMetadata['skipDefault']->xmlCollectionSkipWhenEmpty);
        self::assertFalse($m->propertyMetadata['present']->xmlCollectionSkipWhenEmpty);
    }

    public function testVirtualProperty()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\ObjectWithVirtualProperties'));

        self::assertArrayHasKey('existField', $m->propertyMetadata);
        self::assertArrayHasKey('virtualValue', $m->propertyMetadata);
        self::assertArrayHasKey('virtualSerializedValue', $m->propertyMetadata);
        self::assertArrayHasKey('typedVirtualProperty', $m->propertyMetadata);

        self::assertEquals($m->propertyMetadata['virtualSerializedValue']->serializedName, 'test', 'Serialized name is missing');

        $p = new VirtualPropertyMetadata($m->name, 'virtualValue');
        $p->getter = 'getVirtualValue';
        $p->serializedName = 'virtualValue';

        self::assertEquals($p, $m->propertyMetadata['virtualValue']);
    }

    public function testFirstClassListCollection()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass(FirstClassListCollection::class));
        self::assertTrue($m->isList);
        self::assertFalse($m->isMap);
    }

    public function testFirstClassMapCollection()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass(FirstClassMapCollection::class));
        self::assertFalse($m->isList);
        self::assertTrue($m->isMap);
    }

    public function testXmlKeyValuePairs()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\ObjectWithXmlKeyValuePairs'));

        self::assertArrayHasKey('array', $m->propertyMetadata);
        self::assertTrue($m->propertyMetadata['array']->xmlKeyValuePairs);
    }

    public function testInvalidExpression()
    {
        $this->expectException(InvalidMetadataException::class);

        $a = new ObjectWithInvalidExpression();
        $this->getDriver()->loadMetadataForClass(new \ReflectionClass($a));
    }

    public function testExpressionVirtualPropertyWithExcludeAll()
    {
        $a = new ObjectWithExpressionVirtualPropertiesAndExcludeAll();
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass($a));

        self::assertArrayHasKey('virtualValue', $m->propertyMetadata);

        $p = new ExpressionPropertyMetadata($m->name, 'virtualValue', 'object.getVirtualValue()');
        $p->serializedName = 'virtualValue';
        self::assertEquals($p, $m->propertyMetadata['virtualValue']);
    }

    public function testVirtualPropertyWithExcludeAll()
    {
        $a = new ObjectWithVirtualPropertiesAndExcludeAll();
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass($a));

        self::assertArrayHasKey('virtualValue', $m->propertyMetadata);

        $p = new VirtualPropertyMetadata($m->name, 'virtualValue');
        $p->getter = 'getVirtualValue';
        $p->serializedName = 'virtualValue';

        self::assertEquals($p, $m->propertyMetadata['virtualValue']);
    }

    public function testReadOnlyDefinedBeforeGetterAndSetter()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\AuthorReadOnly'));

        self::assertNotNull($m);
    }

    public function testExpressionVirtualProperty()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\AuthorExpressionAccess'));

        $keys = array_keys($m->propertyMetadata);
        self::assertEquals(['firstName', 'lastName', 'id'], $keys);
    }

    public function testLoadDiscriminator()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Discriminator\Vehicle'));

        self::assertNotNull($m);
        self::assertEquals('type', $m->discriminatorFieldName);
        self::assertEquals($m->name, $m->discriminatorBaseClass);
        self::assertEquals(
            [
                'car' => 'JMS\Serializer\Tests\Fixtures\Discriminator\Car',
                'moped' => 'JMS\Serializer\Tests\Fixtures\Discriminator\Moped',
            ],
            $m->discriminatorMap
        );
    }

    public function testLoadDiscriminatorWhenParentIsInDiscriminatorMap()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Discriminator\Post'));

        self::assertNotNull($m);
        self::assertEquals('type', $m->discriminatorFieldName);
        self::assertEquals($m->name, $m->discriminatorBaseClass);
        self::assertEquals(
            [
                'post' => 'JMS\Serializer\Tests\Fixtures\Discriminator\Post',
                'image_post' => 'JMS\Serializer\Tests\Fixtures\Discriminator\ImagePost',
            ],
            $m->discriminatorMap
        );
    }

    public function testLoadXmlDiscriminator()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass(ObjectWithXmlAttributeDiscriminatorParent::class));

        self::assertNotNull($m);
        self::assertEquals('type', $m->discriminatorFieldName);
        self::assertEquals($m->name, $m->discriminatorBaseClass);
        self::assertEquals(
            [
                'child' => ObjectWithXmlAttributeDiscriminatorChild::class,
            ],
            $m->discriminatorMap
        );
        self::assertTrue($m->xmlDiscriminatorAttribute);
        self::assertFalse($m->xmlDiscriminatorCData);
    }

    public function testLoadXmlDiscriminatorWithNamespaces()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass(ObjectWithXmlNamespaceDiscriminatorParent::class));

        self::assertNotNull($m);
        self::assertEquals('type', $m->discriminatorFieldName);
        self::assertEquals($m->name, $m->discriminatorBaseClass);
        self::assertEquals(
            [
                'child' => ObjectWithXmlNamespaceDiscriminatorChild::class,
            ],
            $m->discriminatorMap
        );
        self::assertEquals('http://example.com/', $m->xmlDiscriminatorNamespace);
        self::assertFalse($m->xmlDiscriminatorAttribute);
    }

    public function testCanDefineMetadataForInternalClass()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass(\PDOStatement::class));

        self::assertNotNull($m);
        self::assertSame('int', $m->propertyMetadata['queryString']->type['name']);

        self::assertCount(1, $m->fileResources);
    }

    public function testLoadXmlDiscriminatorWithAttributeNamespaces()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass(ObjectWithXmlNamespaceAttributeDiscriminatorParent::class));

        self::assertNotNull($m);
        self::assertEquals('type', $m->discriminatorFieldName);
        self::assertEquals($m->name, $m->discriminatorBaseClass);
        self::assertEquals(
            [
                'child' => ObjectWithXmlNamespaceAttributeDiscriminatorChild::class,
            ],
            $m->discriminatorMap
        );
        self::assertEquals('http://example.com/', $m->xmlDiscriminatorNamespace);
        self::assertTrue($m->xmlDiscriminatorAttribute);
    }

    public function testLoadDiscriminatorWithGroup()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\DiscriminatorGroup\Vehicle'));

        self::assertNotNull($m);
        self::assertEquals('type', $m->discriminatorFieldName);
        self::assertEquals(['foo'], $m->discriminatorGroups);
        self::assertEquals($m->name, $m->discriminatorBaseClass);
        self::assertEquals(
            ['car' => 'JMS\Serializer\Tests\Fixtures\DiscriminatorGroup\Car'],
            $m->discriminatorMap
        );
    }

    public function testSkipWhenEmptyOption()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass(ParentSkipWithEmptyChild::class));

        self::assertNotNull($m);

        self::assertInstanceOf(PropertyMetadata::class, $m->propertyMetadata['c']);
        self::assertInstanceOf(PropertyMetadata::class, $m->propertyMetadata['d']);
        self::assertInstanceOf(PropertyMetadata::class, $m->propertyMetadata['child']);
        self::assertFalse($m->propertyMetadata['c']->skipWhenEmpty);
        self::assertFalse($m->propertyMetadata['d']->skipWhenEmpty);
        self::assertTrue($m->propertyMetadata['child']->skipWhenEmpty);
    }

    public function testLoadDiscriminatorSubClass()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Discriminator\Car'));

        self::assertNotNull($m);
        self::assertNull($m->discriminatorValue);
        self::assertNull($m->discriminatorBaseClass);
        self::assertNull($m->discriminatorFieldName);
        self::assertEquals([], $m->discriminatorMap);
    }

    public function testLoadDiscriminatorSubClassWhenParentIsInDiscriminatorMap()
    {
        /** @var ClassMetadata $m */
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Discriminator\ImagePost'));

        self::assertNotNull($m);
        self::assertNull($m->discriminatorValue);
        self::assertNull($m->discriminatorBaseClass);
        self::assertNull($m->discriminatorFieldName);
        self::assertEquals([], $m->discriminatorMap);
    }

    public function testLoadXmlObjectWithNamespacesMetadata()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespaces'));
        self::assertNotNull($m);
        self::assertEquals('test-object', $m->xmlRootName);
        self::assertEquals('ex', $m->xmlRootPrefix);
        self::assertEquals('http://example.com/namespace', $m->xmlRootNamespace);
        self::assertCount(3, $m->xmlNamespaces);
        self::assertArrayHasKey('', $m->xmlNamespaces);
        self::assertEquals('http://example.com/namespace', $m->xmlNamespaces['']);
        self::assertArrayHasKey('gd', $m->xmlNamespaces);
        self::assertEquals('http://schemas.google.com/g/2005', $m->xmlNamespaces['gd']);
        self::assertArrayHasKey('atom', $m->xmlNamespaces);
        self::assertEquals('http://www.w3.org/2005/Atom', $m->xmlNamespaces['atom']);

        $p = new PropertyMetadata($m->name, 'title');
        $p->serializedName = 'title';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://purl.org/dc/elements/1.1/';
        self::assertEquals($p, $m->propertyMetadata['title']);

        $p = new PropertyMetadata($m->name, 'createdAt');
        $p->serializedName = 'createdAt';
        $p->type = ['name' => 'DateTime', 'params' => []];
        $p->xmlAttribute = true;
        self::assertEquals($p, $m->propertyMetadata['createdAt']);

        $p = new PropertyMetadata($m->name, 'etag');
        $p->serializedName = 'etag';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlAttribute = true;
        $p->xmlNamespace = 'http://schemas.google.com/g/2005';
        self::assertEquals($p, $m->propertyMetadata['etag']);

        $p = new PropertyMetadata($m->name, 'author');
        $p->serializedName = 'author';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlAttribute = false;
        $p->xmlNamespace = 'http://www.w3.org/2005/Atom';
        self::assertEquals($p, $m->propertyMetadata['author']);

        $p = new PropertyMetadata($m->name, 'language');
        $p->serializedName = 'language';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlAttribute = true;
        $p->xmlNamespace = 'http://purl.org/dc/elements/1.1/';
        self::assertEquals($p, $m->propertyMetadata['language']);
    }

    public function testMaxDepth()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Node'));

        self::assertEquals(2, $m->propertyMetadata['children']->maxDepth);
    }

    public function testPersonCData()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\Person'));

        self::assertNotNull($m);
        self::assertFalse($m->propertyMetadata['name']->xmlElementCData);
    }

    public function testXmlNamespaceInheritanceMetadata()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\SimpleClassObject'));
        self::assertNotNull($m);
        self::assertCount(3, $m->xmlNamespaces);
        self::assertArrayHasKey('old_foo', $m->xmlNamespaces);
        self::assertEquals('http://old.foo.example.org', $m->xmlNamespaces['old_foo']);
        self::assertArrayHasKey('foo', $m->xmlNamespaces);
        self::assertEquals('http://foo.example.org', $m->xmlNamespaces['foo']);
        self::assertArrayHasKey('new_foo', $m->xmlNamespaces);
        self::assertEquals('http://new.foo.example.org', $m->xmlNamespaces['new_foo']);
        self::assertCount(3, $m->propertyMetadata);

        $p = new PropertyMetadata($m->name, 'foo');
        $p->serializedName = 'foo';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://old.foo.example.org';
        $p->xmlAttribute = true;
        self::assertEquals($p, $m->propertyMetadata['foo']);

        $p = new PropertyMetadata($m->name, 'bar');
        $p->serializedName = 'bar';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://foo.example.org';
        self::assertEquals($p, $m->propertyMetadata['bar']);

        $p = new PropertyMetadata($m->name, 'moo');
        $p->serializedName = 'moo';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://new.foo.example.org';
        self::assertEquals($p, $m->propertyMetadata['moo']);

        $subm = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\SimpleSubClassObject'));
        self::assertNotNull($subm);
        self::assertCount(2, $subm->xmlNamespaces);
        self::assertArrayHasKey('old_foo', $subm->xmlNamespaces);
        self::assertEquals('http://foo.example.org', $subm->xmlNamespaces['old_foo']);
        self::assertArrayHasKey('foo', $subm->xmlNamespaces);
        self::assertEquals('http://better.foo.example.org', $subm->xmlNamespaces['foo']);
        self::assertCount(3, $subm->propertyMetadata);

        $p = new PropertyMetadata($subm->name, 'moo');
        $p->serializedName = 'moo';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://better.foo.example.org';
        self::assertEquals($p, $subm->propertyMetadata['moo']);

        $p = new PropertyMetadata($subm->name, 'baz');
        $p->serializedName = 'baz';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://foo.example.org';
        self::assertEquals($p, $subm->propertyMetadata['baz']);

        $p = new PropertyMetadata($subm->name, 'qux');
        $p->serializedName = 'qux';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://new.foo.example.org';
        self::assertEquals($p, $subm->propertyMetadata['qux']);

        $m->merge($subm);
        self::assertNotNull($m);
        self::assertCount(3, $m->xmlNamespaces);
        self::assertArrayHasKey('old_foo', $m->xmlNamespaces);
        self::assertEquals('http://foo.example.org', $m->xmlNamespaces['old_foo']);
        self::assertArrayHasKey('foo', $m->xmlNamespaces);
        self::assertEquals('http://better.foo.example.org', $m->xmlNamespaces['foo']);
        self::assertArrayHasKey('new_foo', $m->xmlNamespaces);
        self::assertEquals('http://new.foo.example.org', $m->xmlNamespaces['new_foo']);
        self::assertCount(5, $m->propertyMetadata);

        $p = new PropertyMetadata($m->name, 'foo');
        $p->serializedName = 'foo';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://old.foo.example.org';
        $p->xmlAttribute = true;
        $p->class = 'JMS\Serializer\Tests\Fixtures\SimpleClassObject';
        $this->assetMetadataEquals($p, $m->propertyMetadata['foo']);

        $p = new PropertyMetadata($m->name, 'bar');
        $p->serializedName = 'bar';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://foo.example.org';
        $p->class = 'JMS\Serializer\Tests\Fixtures\SimpleClassObject';
        $this->assetMetadataEquals($p, $m->propertyMetadata['bar']);

        $p = new PropertyMetadata($m->name, 'moo');
        $p->serializedName = 'moo';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://better.foo.example.org';
        $this->assetMetadataEquals($p, $m->propertyMetadata['moo']);

        $p = new PropertyMetadata($m->name, 'baz');
        $p->serializedName = 'baz';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://foo.example.org';
        $this->assetMetadataEquals($p, $m->propertyMetadata['baz']);

        $p = new PropertyMetadata($m->name, 'qux');
        $p->serializedName = 'qux';
        $p->type = ['name' => 'string', 'params' => []];
        $p->xmlNamespace = 'http://new.foo.example.org';
        $this->assetMetadataEquals($p, $m->propertyMetadata['qux']);
    }

    private function assetMetadataEquals(PropertyMetadata $expected, PropertyMetadata $actual)
    {
        $expectedVars = get_object_vars($expected);
        $actualVars = get_object_vars($actual);

        self::assertEquals($expectedVars, $actualVars);
    }

    public function testExclusionIf()
    {
        $class = 'JMS\Serializer\Tests\Fixtures\PersonSecret';
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass($class));

        $p = new PropertyMetadata($class, 'name');
        $p->serializedName = 'name';
        $p->type = ['name' => 'string', 'params' => []];
        self::assertEquals($p, $m->propertyMetadata['name']);

        $p = new PropertyMetadata($class, 'gender');
        $p->serializedName = 'gender';
        $p->type = ['name' => 'string', 'params' => []];
        $p->excludeIf = "show_data('gender')";
        self::assertEquals($p, $m->propertyMetadata['gender']);

        $p = new PropertyMetadata($class, 'age');
        $p->serializedName = 'age';
        $p->type = ['name' => 'string', 'params' => []];
        $p->excludeIf = "!(show_data('age'))";
        self::assertEquals($p, $m->propertyMetadata['age']);
    }

    public function testObjectWithVirtualPropertiesAndDuplicatePropName()
    {
        $class = ObjectWithVirtualPropertiesAndDuplicatePropName::class;
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass($class));

        $p = new PropertyMetadata($class, 'id');
        $p->serializedName = 'id';
        self::assertEquals($p, $m->propertyMetadata['id']);

        $p = new PropertyMetadata($class, 'name');
        $p->serializedName = 'name';
        self::assertEquals($p, $m->propertyMetadata['name']);

        $p = new VirtualPropertyMetadata($class, 'foo');
        $p->serializedName = 'id';
        $p->getter = 'getId';

        self::assertEquals($p, $m->propertyMetadata['foo']);

        $p = new VirtualPropertyMetadata($class, 'bar');
        $p->serializedName = 'mood';
        $p->getter = 'getName';

        self::assertEquals($p, $m->propertyMetadata['bar']);
    }

    public function testExcludePropertyNoPublicAccessorException()
    {
        $first = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\ExcludePublicAccessor'));

        if ($this instanceof PhpDriverTest) {
            return;
        }
        self::assertArrayHasKey('id', $first->propertyMetadata);
        self::assertArrayNotHasKey('iShallNotBeAccessed', $first->propertyMetadata);
    }

    /**
     * @return DriverInterface
     */
    abstract protected function getDriver();

    protected function getExpressionEvaluator()
    {
        $language = new ExpressionLanguage();

        $language->addFunction(new ExpressionFunction('show_data', static function () {
            return 'true';
        }, static function () {
            return true;
        }));
        return new ExpressionEvaluator($language);
    }
}
