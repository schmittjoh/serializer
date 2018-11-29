<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber;
use JMS\Serializer\Exclusion\DepthExclusionStrategy;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\ArrayCollectionHandler;
use JMS\Serializer\Handler\ConstraintViolationHandler;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\Handler\FormErrorHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\Handler\StdClassHandler;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\AccessorOrderChild;
use JMS\Serializer\Tests\Fixtures\AccessorOrderMethod;
use JMS\Serializer\Tests\Fixtures\AccessorOrderParent;
use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\AuthorExpressionAccess;
use JMS\Serializer\Tests\Fixtures\AuthorExpressionAccessContext;
use JMS\Serializer\Tests\Fixtures\AuthorList;
use JMS\Serializer\Tests\Fixtures\AuthorReadOnly;
use JMS\Serializer\Tests\Fixtures\AuthorReadOnlyPerClass;
use JMS\Serializer\Tests\Fixtures\AuthorsInline;
use JMS\Serializer\Tests\Fixtures\BlogPost;
use JMS\Serializer\Tests\Fixtures\CircularReferenceCollection;
use JMS\Serializer\Tests\Fixtures\CircularReferenceParent;
use JMS\Serializer\Tests\Fixtures\Comment;
use JMS\Serializer\Tests\Fixtures\CurrencyAwareOrder;
use JMS\Serializer\Tests\Fixtures\CurrencyAwarePrice;
use JMS\Serializer\Tests\Fixtures\CustomDeserializationObject;
use JMS\Serializer\Tests\Fixtures\DateTimeArraysObject;
use JMS\Serializer\Tests\Fixtures\Discriminator\Car;
use JMS\Serializer\Tests\Fixtures\Discriminator\ImagePost;
use JMS\Serializer\Tests\Fixtures\Discriminator\Moped;
use JMS\Serializer\Tests\Fixtures\Discriminator\Post;
use JMS\Serializer\Tests\Fixtures\DiscriminatorGroup\Car as DiscriminatorGroupCar;
use JMS\Serializer\Tests\Fixtures\ExclusionStrategy\AlwaysExcludeExclusionStrategy;
use JMS\Serializer\Tests\Fixtures\FirstClassListCollection;
use JMS\Serializer\Tests\Fixtures\Garage;
use JMS\Serializer\Tests\Fixtures\GetSetObject;
use JMS\Serializer\Tests\Fixtures\GroupsObject;
use JMS\Serializer\Tests\Fixtures\GroupsUser;
use JMS\Serializer\Tests\Fixtures\IndexedCommentsBlogPost;
use JMS\Serializer\Tests\Fixtures\InitializedBlogPostConstructor;
use JMS\Serializer\Tests\Fixtures\InitializedObjectConstructor;
use JMS\Serializer\Tests\Fixtures\InlineChild;
use JMS\Serializer\Tests\Fixtures\InlineChildEmpty;
use JMS\Serializer\Tests\Fixtures\InlineChildWithGroups;
use JMS\Serializer\Tests\Fixtures\InlineParent;
use JMS\Serializer\Tests\Fixtures\InlineParentWithEmptyChild;
use JMS\Serializer\Tests\Fixtures\Input;
use JMS\Serializer\Tests\Fixtures\InvalidGroupsObject;
use JMS\Serializer\Tests\Fixtures\Log;
use JMS\Serializer\Tests\Fixtures\MaxDepth\Gh236Foo;
use JMS\Serializer\Tests\Fixtures\NamedDateTimeArraysObject;
use JMS\Serializer\Tests\Fixtures\NamedDateTimeImmutableArraysObject;
use JMS\Serializer\Tests\Fixtures\Node;
use JMS\Serializer\Tests\Fixtures\ObjectUsingTypeCasting;
use JMS\Serializer\Tests\Fixtures\ObjectWithEmptyHash;
use JMS\Serializer\Tests\Fixtures\ObjectWithEmptyNullableAndEmptyArrays;
use JMS\Serializer\Tests\Fixtures\ObjectWithIntListAndIntMap;
use JMS\Serializer\Tests\Fixtures\ObjectWithLifecycleCallbacks;
use JMS\Serializer\Tests\Fixtures\ObjectWithNullProperty;
use JMS\Serializer\Tests\Fixtures\ObjectWithToString;
use JMS\Serializer\Tests\Fixtures\ObjectWithTypedArraySetter;
use JMS\Serializer\Tests\Fixtures\ObjectWithVersionedVirtualProperties;
use JMS\Serializer\Tests\Fixtures\ObjectWithVirtualProperties;
use JMS\Serializer\Tests\Fixtures\Order;
use JMS\Serializer\Tests\Fixtures\ParentDoNotSkipWithEmptyChild;
use JMS\Serializer\Tests\Fixtures\ParentSkipWithEmptyChild;
use JMS\Serializer\Tests\Fixtures\PersonSecret;
use JMS\Serializer\Tests\Fixtures\PersonSecretMore;
use JMS\Serializer\Tests\Fixtures\PersonSecretMoreVirtual;
use JMS\Serializer\Tests\Fixtures\PersonSecretVirtual;
use JMS\Serializer\Tests\Fixtures\Price;
use JMS\Serializer\Tests\Fixtures\Publisher;
use JMS\Serializer\Tests\Fixtures\SimpleInternalObject;
use JMS\Serializer\Tests\Fixtures\SimpleObject;
use JMS\Serializer\Tests\Fixtures\SimpleObjectProxy;
use JMS\Serializer\Tests\Fixtures\SimpleObjectWithStaticProp;
use JMS\Serializer\Tests\Fixtures\Tag;
use JMS\Serializer\Tests\Fixtures\Timestamp;
use JMS\Serializer\Tests\Fixtures\Tree;
use JMS\Serializer\Tests\Fixtures\VehicleInterfaceGarage;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

abstract class BaseSerializationTest extends TestCase
{
    protected $factory;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /** @var Serializer */
    protected $serializer;

    /**
     * @var HandlerRegistryInterface
     */
    protected $handlerRegistry;
    protected $serializationVisitors;
    protected $deserializationVisitors;
    protected $objectConstructor;
    protected $accessorStrategy;

    public function testSerializeNullArray()
    {
        $arr = ['foo' => 'bar', 'baz' => null, null];

        self::assertEquals(
            $this->getContent('nullable'),
            $this->serializer->serialize($arr, $this->getFormat(), SerializationContext::create()->setSerializeNull(true))
        );
    }

    public function testDeserializeObjectWithMissingTypedArrayProp()
    {
        /** @var ObjectWithTypedArraySetter $dObj */
        $dObj = $this->serializer->deserialize(
            $this->getContent('empty_object'),
            ObjectWithTypedArraySetter::class,
            $this->getFormat()
        );

        self::assertInstanceOf(ObjectWithTypedArraySetter::class, $dObj);

        self::assertSame([], $dObj->getEmpty());
    }

    public function testSerializeNullArrayExcludingNulls()
    {
        $arr = ['foo' => 'bar', 'baz' => null, null];

        self::assertEquals(
            $this->getContent('nullable_skip'),
            $this->serializer->serialize($arr, $this->getFormat(), SerializationContext::create()->setSerializeNull(false))
        );
    }

    public function testObjectUsingTypeCasting()
    {
        $typeAliasing = new ObjectUsingTypeCasting();
        $typeAliasing->asString = new ObjectWithToString('8');

        self::assertEquals(
            $this->getContent('type_casting'),
            $this->serialize($typeAliasing)
        );
    }

    public function testSerializeNullObject()
    {
        $obj = new ObjectWithNullProperty('foo', 'bar');

        self::assertEquals(
            $this->getContent('simple_object_nullable'),
            $this->serializer->serialize($obj, $this->getFormat(), SerializationContext::create()->setSerializeNull(true))
        );
    }

    public function testDeserializeNullObject()
    {
        if (!$this->hasDeserializer()) {
            $this->markTestSkipped(sprintf('No deserializer available for format `%s`', $this->getFormat()));
        }

        $obj = new ObjectWithNullProperty('foo', 'bar');

        /** @var ObjectWithNullProperty $dObj */
        $dObj = $this->serializer->deserialize(
            $this->getContent('simple_object_nullable'),
            ObjectWithNullProperty::class,
            $this->getFormat()
        );

        self::assertEquals($obj, $dObj);
        self::assertNull($dObj->getNullProperty());
    }

    /**
     * @expectedException \JMS\Serializer\Exception\NotAcceptableException
     * @dataProvider getTypes
     */
    public function testNull($type)
    {
        if ($this->hasDeserializer()) {
            self::assertEquals(null, $this->deserialize($this->getContent('null'), $type));
        }

        // this is the default, but we want to be explicit here
        $context = SerializationContext::create()->setSerializeNull(false);
        $this->serialize(null, $context);
    }

    /**
     * @dataProvider getTypes
     */
    public function testNullAllowed($type)
    {
        $context = SerializationContext::create()->setSerializeNull(true);
        self::assertEquals($this->getContent('null'), $this->serialize(null, $context), $type);

        if ($this->hasDeserializer()) {
            self::assertEquals(null, $this->deserialize($this->getContent('null'), $type));
        }
    }

    public function getTypes()
    {
        return [
            ['NULL'],
            ['integer'],
            ['double'],
            ['float'],
            ['string'],
            ['DateTime'],
        ];
    }

    public function testString()
    {
        self::assertEquals($this->getContent('string'), $this->serialize('foo'));

        if ($this->hasDeserializer()) {
            self::assertEquals('foo', $this->deserialize($this->getContent('string'), 'string'));
        }
    }

    /**
     * @expectedException \JMS\Serializer\Exception\ExpressionLanguageRequiredException
     * @expectedExceptionMessage To use conditional exclude/expose in JMS\Serializer\Tests\Fixtures\PersonSecret you must configure the expression language.
     */
    public function testExpressionExclusionNotConfigured()
    {
        $person = new PersonSecret();
        $person->gender = 'f';
        $person->name = 'mike';
        $this->serialize($person);
    }

    public function testExpressionExclusionConfiguredWithDisjunctStrategy()
    {
        $person = new PersonSecret();
        $person->gender = 'f';
        $person->name = 'mike';

        $language = new ExpressionLanguage();
        $language->addFunction(new ExpressionFunction('show_data', static function () {
            return 'true';
        }, static function () {
            return true;
        }));

        $builder = SerializerBuilder::create();
        $builder->setExpressionEvaluator(new ExpressionEvaluator($language));
        $serializer = $builder->build();

        self::assertEquals($this->getContent('person_secret_hide'), $serializer->serialize($person, $this->getFormat()));
    }

    public function expressionFunctionProvider()
    {
        $person = new PersonSecret();
        $person->gender = 'f';
        $person->name = 'mike';

        $personMoreSecret = new PersonSecretMore();
        $personMoreSecret->gender = 'f';
        $personMoreSecret->name = 'mike';

        $personVirtual = new PersonSecretVirtual();
        $personVirtual->gender = 'f';
        $personVirtual->name = 'mike';

        $personMoreSecretVirtual = new PersonSecretMoreVirtual();
        $personMoreSecretVirtual->gender = 'f';
        $personMoreSecretVirtual->name = 'mike';

        $showGender = new ExpressionFunction('show_data', static function () {
            return 'true';
        }, static function () {
            return true;
        });

        $hideGender = new ExpressionFunction('show_data', static function () {
            return 'false';
        }, static function () {
            return false;
        });

        return [
            [
                $person,
                $showGender,
                'person_secret_hide',
            ],
            [
                $person,
                $hideGender,
                'person_secret_show',
            ],
            [
                $personMoreSecret,
                $showGender,
                'person_secret_show',
            ],
            [
                $personMoreSecret,
                $hideGender,
                'person_secret_hide',
            ],
            [
                $personVirtual,
                $showGender,
                'person_secret_hide',
            ],
            [
                $personVirtual,
                $hideGender,
                'person_secret_show',
            ],
            [
                $personMoreSecretVirtual,
                $showGender,
                'person_secret_show',
            ],
            [
                $personMoreSecretVirtual,
                $hideGender,
                'person_secret_hide',
            ],
        ];
    }

    /**
     * @param PersonSecret|PersonSecretMore $person
     * @param ExpressionFunction $function
     * @param string $json
     *
     * @dataProvider expressionFunctionProvider
     */
    public function testExpressionExclusion($person, ExpressionFunction $function, $json)
    {
        $language = new ExpressionLanguage();
        $language->addFunction($function);

        $builder = SerializerBuilder::create();
        $builder->setExpressionEvaluator(new ExpressionEvaluator($language));
        $serializer = $builder->build();

        self::assertEquals($this->getContent($json), $serializer->serialize($person, $this->getFormat()));
    }

    /**
     * @dataProvider getBooleans
     */
    public function testBooleans($strBoolean, $boolean)
    {
        self::assertEquals($this->getContent('boolean_' . $strBoolean), $this->serialize($boolean));

        if ($this->hasDeserializer()) {
            self::assertSame($boolean, $this->deserialize($this->getContent('boolean_' . $strBoolean), 'boolean'));
        }
    }

    public function getBooleans()
    {
        return [['true', true], ['false', false]];
    }

    /**
     * @dataProvider getNumerics
     */
    public function testNumerics($key, $value, $type)
    {
        self::assertSame($this->getContent($key), $this->serialize($value));

        if ($this->hasDeserializer()) {
            self::assertEquals($value, $this->deserialize($this->getContent($key), $type));
        }
    }

    public function getNumerics()
    {
        return [
            ['integer', 1, 'integer'],
            ['float', 4.533, 'double'],
            ['float', 4.533, 'float'],
            ['float_trailing_zero', 1.0, 'double'],
            ['float_trailing_zero', 1.0, 'float'],
        ];
    }

    public function testSimpleInternalObject()
    {
        $builder = SerializerBuilder::create($this->handlerRegistry, $this->dispatcher);
        $builder->setMetadataDirs([
            'JMS\Serializer\Tests\Fixtures' => __DIR__ . '/metadata/SimpleInternalObject',
            '' => __DIR__ . '/metadata/SimpleInternalObject',
        ]);

        $this->serializer = $builder->build();

        $obj = new SimpleInternalObject('foo', 'bar');

        $this->assertEquals($this->getContent('simple_object'), $this->serialize($obj));

        if ($this->hasDeserializer()) {
            $this->assertEquals($obj, $this->deserialize($this->getContent('simple_object'), get_class($obj)));
        }
    }

    public function testSimpleObject()
    {
        self::assertEquals($this->getContent('simple_object'), $this->serialize($obj = new SimpleObject('foo', 'bar')));

        if ($this->hasDeserializer()) {
            self::assertEquals($obj, $this->deserialize($this->getContent('simple_object'), get_class($obj)));
        }
    }

    public function testSimpleObjectStaticProp()
    {
        $this->assertEquals($this->getContent('simple_object'), $this->serialize($obj = new SimpleObjectWithStaticProp('foo', 'bar')));

        if ($this->hasDeserializer()) {
            $this->assertEquals($obj, $this->deserialize($this->getContent('simple_object'), get_class($obj)));
        }
    }

    public function testArrayStrings()
    {
        $data = ['foo', 'bar'];
        self::assertEquals($this->getContent('array_strings'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            self::assertEquals($data, $this->deserialize($this->getContent('array_strings'), 'array<string>'));
        }
    }

    public function testArrayBooleans()
    {
        $data = [true, false];
        self::assertEquals($this->getContent('array_booleans'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            self::assertEquals($data, $this->deserialize($this->getContent('array_booleans'), 'array<boolean>'));
        }
    }

    public function testArrayIntegers()
    {
        $data = [1, 3, 4];
        self::assertEquals($this->getContent('array_integers'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            self::assertEquals($data, $this->deserialize($this->getContent('array_integers'), 'array<integer>'));
        }
    }

    public function testArrayEmpty()
    {
        if ('xml' === $this->getFormat()) {
            $this->markTestSkipped('XML can\'t be tested for empty array');
        }

        $data = ['array' => []];
        self::assertEquals($this->getContent('array_empty'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            self::assertEquals($data, $this->deserialize($this->getContent('array_empty'), 'array'));
        }
    }

    public function testArrayFloats()
    {
        $data = [1.34, 3.0, 6.42];
        self::assertEquals($this->getContent('array_floats'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            self::assertEquals($data, $this->deserialize($this->getContent('array_floats'), 'array<double>'));
        }
    }

    public function testArrayObjects()
    {
        $data = [new SimpleObject('foo', 'bar'), new SimpleObject('baz', 'boo')];
        self::assertEquals($this->getContent('array_objects'), $this->serialize($data));

        if ($this->hasDeserializer()) {
            self::assertEquals($data, $this->deserialize($this->getContent('array_objects'), 'array<JMS\Serializer\Tests\Fixtures\SimpleObject>'));
        }
    }

    public function testArrayListAndMapDifference()
    {
        $arrayData = [0 => 1, 2 => 2, 3 => 3]; // Misses key 1
        $data = new ObjectWithIntListAndIntMap($arrayData, $arrayData);

        self::assertEquals($this->getContent('array_list_and_map_difference'), $this->serialize($data));
    }

    public function testDateTimeArrays()
    {
        $data = [
            new \DateTime('2047-01-01 12:47:47', new \DateTimeZone('UTC')),
            new \DateTime('2016-12-05 00:00:00', new \DateTimeZone('UTC')),
        ];

        $object = new DateTimeArraysObject($data, $data);
        $serializedObject = $this->serialize($object);

        self::assertEquals($this->getContent('array_datetimes_object'), $serializedObject);

        if ($this->hasDeserializer()) {
            /** @var DateTimeArraysObject $deserializedObject */
            $deserializedObject = $this->deserialize($this->getContent('array_datetimes_object'), 'Jms\Serializer\Tests\Fixtures\DateTimeArraysObject');

            /** deserialized object has a default timezone set depending on user's timezone settings. That's why we manually set the UTC timezone on the DateTime objects. */
            foreach ($deserializedObject->getArrayWithDefaultDateTime() as $dateTime) {
                $dateTime->setTimezone(new \DateTimeZone('UTC'));
            }

            foreach ($deserializedObject->getArrayWithFormattedDateTime() as $dateTime) {
                $dateTime->setTimezone(new \DateTimeZone('UTC'));
            }

            self::assertEquals($object, $deserializedObject);
        }
    }

    public function testNamedDateTimeArrays()
    {
        $data = [
            new \DateTime('2047-01-01 12:47:47', new \DateTimeZone('UTC')),
            new \DateTime('2016-12-05 00:00:00', new \DateTimeZone('UTC')),
        ];

        $object = new NamedDateTimeArraysObject(['testdate1' => $data[0], 'testdate2' => $data[1]]);
        $serializedObject = $this->serialize($object);

        self::assertEquals($this->getContent('array_named_datetimes_object'), $serializedObject);

        if ($this->hasDeserializer()) {
            // skip XML deserialization
            if ('xml' === $this->getFormat()) {
                return;
            }

            /** @var NamedDateTimeArraysObject $deserializedObject */
            $deserializedObject = $this->deserialize($this->getContent('array_named_datetimes_object'), 'Jms\Serializer\Tests\Fixtures\NamedDateTimeArraysObject');

            /** deserialized object has a default timezone set depending on user's timezone settings. That's why we manually set the UTC timezone on the DateTime objects. */
            foreach ($deserializedObject->getNamedArrayWithFormattedDate() as $dateTime) {
                $dateTime->setTimezone(new \DateTimeZone('UTC'));
            }

            self::assertEquals($object, $deserializedObject);
        }
    }

    /**
     * @group datetime
     */
    public function testNamedDateTimeImmutableArrays()
    {
        $data = [
            new \DateTimeImmutable('2047-01-01 12:47:47', new \DateTimeZone('UTC')),
            new \DateTimeImmutable('2016-12-05 00:00:00', new \DateTimeZone('UTC')),
        ];

        $object = new NamedDateTimeImmutableArraysObject(['testdate1' => $data[0], 'testdate2' => $data[1]]);
        $serializedObject = $this->serialize($object);

        self::assertEquals($this->getContent('array_named_datetimeimmutables_object'), $serializedObject);

        if ($this->hasDeserializer()) {
            if ('xml' === $this->getFormat()) {
                $this->markTestSkipped('XML deserialization does not support key-val pairs mode');
            }
            /** @var NamedDateTimeArraysObject $deserializedObject */
            $deserializedObject = $this->deserialize($this->getContent('array_named_datetimeimmutables_object'), 'Jms\Serializer\Tests\Fixtures\NamedDateTimeImmutableArraysObject');

            /** deserialized object has a default timezone set depending on user's timezone settings. That's why we manually set the UTC timezone on the DateTime objects. */
            foreach ($deserializedObject->getNamedArrayWithFormattedDate() as $dateTime) {
                $dateTime->setTimezone(new \DateTimeZone('UTC'));
            }

            self::assertEquals($object, $deserializedObject);
        }
    }

    public function testArrayMixed()
    {
        self::assertEquals($this->getContent('array_mixed'), $this->serialize(['foo', 1, true, new SimpleObject('foo', 'bar'), [1, 3, true]]));
    }

    /**
     * @dataProvider getDateTime
     * @group datetime
     */
    public function testDateTime($key, $value, $type)
    {
        self::assertEquals($this->getContent($key), $this->serialize($value));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent($key), $type);

            self::assertInternalType('object', $deserialized);
            self::assertInstanceOf(get_class($value), $deserialized);
            self::assertEquals($value->getTimestamp(), $deserialized->getTimestamp());
        }
    }

    public function getDateTime()
    {
        return [
            ['date_time', new \DateTime('2011-08-30 00:00', new \DateTimeZone('UTC')), 'DateTime'],
        ];
    }

    /**
     * @dataProvider getDateTimeImmutable
     * @group datetime
     */
    public function testDateTimeImmutable($key, $value, $type)
    {
        self::assertEquals($this->getContent($key), $this->serialize($value));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent($key), $type);

            self::assertInternalType('object', $deserialized);
            self::assertInstanceOf(get_class($value), $deserialized);
            self::assertEquals($value->getTimestamp(), $deserialized->getTimestamp());
        }
    }

    public function getDateTimeImmutable()
    {
        return [
            ['date_time_immutable', new \DateTimeImmutable('2011-08-30 00:00', new \DateTimeZone('UTC')), 'DateTimeImmutable'],
        ];
    }

    public function testTimestamp()
    {
        $value = new Timestamp(new \DateTime('2016-02-11 00:00:00', new \DateTimeZone('UTC')));
        self::assertEquals($this->getContent('timestamp'), $this->serialize($value));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('timestamp'), Timestamp::class);
            self::assertEquals($value, $deserialized);
            self::assertEquals($value->getTimestamp()->getTimestamp(), $deserialized->getTimestamp()->getTimestamp());

            $deserialized = $this->deserialize($this->getContent('timestamp_prev'), Timestamp::class);
            self::assertEquals($value, $deserialized);
            self::assertEquals($value->getTimestamp()->getTimestamp(), $deserialized->getTimestamp()->getTimestamp());
        }
    }

    public function testDateInterval()
    {
        $duration = new \DateInterval('PT45M');

        self::assertEquals($this->getContent('date_interval'), $this->serializer->serialize($duration, $this->getFormat()));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('date_interval'), \DateInterval::class);
            self::assertEquals($duration, $deserialized);
            self::assertEquals($duration->i, $deserialized->i);
        }
    }

    public function testBlogPost()
    {
        $post = new BlogPost('This is a nice title.', $author = new Author('Foo Bar'), new \DateTime('2011-07-30 00:00', new \DateTimeZone('UTC')), new Publisher('Bar Foo'));
        $post->addComment($comment = new Comment($author, 'foo'));

        $post->addTag($tag1 = new Tag('tag1'));
        $post->addTag($tag2 = new Tag('tag2'));

        self::assertEquals($this->getContent('blog_post'), $this->serialize($post));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('blog_post'), get_class($post));
            self::assertEquals('2011-07-30T00:00:00+00:00', $this->getField($deserialized, 'createdAt')->format(\DateTime::ATOM));
            self::assertAttributeEquals('This is a nice title.', 'title', $deserialized);
            self::assertAttributeSame(false, 'published', $deserialized);
            self::assertAttributeSame(false, 'reviewed', $deserialized);
            self::assertAttributeSame('e86ce85cdb1253e4fc6352f5cf297248bceec62b', 'etag', $deserialized);
            self::assertAttributeEquals(new ArrayCollection([$comment]), 'comments', $deserialized);
            self::assertAttributeEquals([$comment], 'comments2', $deserialized);
            self::assertAttributeEquals($author, 'author', $deserialized);
            self::assertAttributeEquals([$tag1, $tag2], 'tag', $deserialized);
        }
    }

    public function testDeserializingNull()
    {
        $objectConstructor = new InitializedBlogPostConstructor();

        $builder = SerializerBuilder::create();
        $builder->setObjectConstructor($objectConstructor);
        $this->serializer = $builder->build();

        $post = new BlogPost('This is a nice title.', $author = new Author('Foo Bar'), new \DateTime('2011-07-30 00:00', new \DateTimeZone('UTC')), new Publisher('Bar Foo'));

        $this->setField($post, 'author', null);
        $this->setField($post, 'publisher', null);

        self::assertEquals($this->getContent('blog_post_unauthored'), $this->serialize($post, SerializationContext::create()->setSerializeNull(true)));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('blog_post_unauthored'), get_class($post), DeserializationContext::create());

            self::assertEquals('2011-07-30T00:00:00+00:00', $this->getField($deserialized, 'createdAt')->format(\DateTime::ATOM));
            self::assertAttributeEquals('This is a nice title.', 'title', $deserialized);
            self::assertAttributeSame(false, 'published', $deserialized);
            self::assertAttributeSame(false, 'reviewed', $deserialized);
            self::assertAttributeEquals(new ArrayCollection(), 'comments', $deserialized);
            self::assertEquals(null, $this->getField($deserialized, 'author'));
        }
    }

    public function testExpressionAuthor()
    {
        $evaluator = new ExpressionEvaluator(new ExpressionLanguage());

        $builder = SerializerBuilder::create();
        $builder->setExpressionEvaluator($evaluator);
        $serializer = $builder->build();

        $author = new AuthorExpressionAccess(123, 'Ruud', 'Kamphuis');
        self::assertEquals($this->getContent('author_expression'), $serializer->serialize($author, $this->getFormat()));
    }

    public function testExpressionAuthorWithContextVars()
    {
        $evaluator = new ExpressionEvaluator(new ExpressionLanguage());

        $builder = SerializerBuilder::create();
        $builder->setExpressionEvaluator($evaluator);
        $serializer = $builder->build();

        $author = new AuthorExpressionAccessContext('Ruud');
        self::assertEquals($this->getContent('author_expression_context'), $serializer->serialize($author, $this->getFormat()));
    }


    /**
     * @expectedException \JMS\Serializer\Exception\ExpressionLanguageRequiredException
     * @expectedExceptionMessage The property firstName on JMS\Serializer\Tests\Fixtures\AuthorExpressionAccess requires the expression accessor strategy to be enabled.
     */
    public function testExpressionAccessorStrategNotEnabled()
    {
        $author = new AuthorExpressionAccess(123, 'Ruud', 'Kamphuis');
        self::assertEquals($this->getContent('author_expression'), $this->serialize($author));
    }

    public function testReadOnly()
    {
        $author = new AuthorReadOnly(123, 'Ruud Kamphuis');
        self::assertEquals($this->getContent('readonly'), $this->serialize($author));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('readonly'), get_class($author));
            self::assertNull($this->getField($deserialized, 'id'));
            self::assertEquals('Ruud Kamphuis', $this->getField($deserialized, 'name'));
        }
    }

    public function testReadOnlyClass()
    {
        $author = new AuthorReadOnlyPerClass(123, 'Ruud Kamphuis');
        self::assertEquals($this->getContent('readonly'), $this->serialize($author));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('readonly'), get_class($author));
            self::assertNull($this->getField($deserialized, 'id'));
            self::assertEquals('Ruud Kamphuis', $this->getField($deserialized, 'name'));
        }
    }

    public function testPrice()
    {
        $price = new Price(3);
        self::assertEquals($this->getContent('price'), $this->serialize($price));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('price'), get_class($price));
            self::assertEquals(3, $this->getField($deserialized, 'price'));
        }
    }

    public function testOrder()
    {
        $order = new Order(new Price(12.34));
        self::assertEquals($this->getContent('order'), $this->serialize($order));

        if ($this->hasDeserializer()) {
            self::assertEquals($order, $this->deserialize($this->getContent('order'), get_class($order)));
        }
    }

    public function testCurrencyAwarePrice()
    {
        $price = new CurrencyAwarePrice(2.34);
        self::assertEquals($this->getContent('currency_aware_price'), $this->serialize($price));

        if ($this->hasDeserializer()) {
            self::assertEquals($price, $this->deserialize($this->getContent('currency_aware_price'), get_class($price)));
        }
    }

    public function testOrderWithCurrencyAwarePrice()
    {
        $order = new CurrencyAwareOrder(new CurrencyAwarePrice(1.23));
        self::assertEquals($this->getContent('order_with_currency_aware_price'), $this->serialize($order));

        if ($this->hasDeserializer()) {
            self::assertEquals($order, $this->deserialize($this->getContent('order_with_currency_aware_price'), get_class($order)));
        }
    }

    public function testInline()
    {
        $inline = new InlineParent();

        $result = $this->serialize($inline);
        self::assertEquals($this->getContent('inline'), $result);

        if ($this->hasDeserializer()) {
            self::assertEquals($inline, $this->deserialize($this->getContent('inline'), get_class($inline)));
        }
    }

    public function testInlineEmptyChild()
    {
        $inline = new InlineParentWithEmptyChild(new InlineChildEmpty());
        $result = $this->serialize($inline);
        self::assertEquals($this->getContent('inline_child_empty'), $result);
        if ($this->hasDeserializer()) {
            self::assertEquals($inline, $this->deserialize($this->getContent('inline'), get_class($inline)));
        }
    }

    public function testEmptyChild()
    {
        // by empty object
        $inline = new ParentDoNotSkipWithEmptyChild(new InlineChildEmpty());
        self::assertEquals($this->getContent('empty_child'), $this->serialize($inline));

        // by nulls
        $inner = new InlineChild();
        $inner->a = null;
        $inner->b = null;
        $inline = new ParentDoNotSkipWithEmptyChild($inner);
        self::assertEquals($this->getContent('empty_child'), $this->serialize($inline));

        // by exclusion strategy
        $context = SerializationContext::create()->setGroups(['Default']);
        $inline = new ParentDoNotSkipWithEmptyChild(new InlineChildWithGroups());
        self::assertEquals($this->getContent('empty_child'), $this->serialize($inline, $context));
    }

    public function testSkipEmptyChild()
    {
        // by empty object
        $inline = new ParentSkipWithEmptyChild(new InlineChildEmpty());
        self::assertEquals($this->getContent('empty_child_skip'), $this->serialize($inline));

        // by nulls
        $inner = new InlineChild();
        $inner->a = null;
        $inner->b = null;
        $inline = new ParentSkipWithEmptyChild($inner);
        self::assertEquals($this->getContent('empty_child_skip'), $this->serialize($inline));

        // by exclusion strategy
        $context = SerializationContext::create()->setGroups(['Default']);
        $inline = new ParentSkipWithEmptyChild(new InlineChildWithGroups());
        self::assertEquals($this->getContent('empty_child_skip'), $this->serialize($inline, $context));
    }

    public function testLog()
    {
        self::assertEquals($this->getContent('log'), $this->serialize($log = new Log()));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('log'), get_class($log));
            self::assertEquals($log, $deserialized);
        }
    }

    public function testSelfCircularReferenceCollection()
    {
        $object = new CircularReferenceCollection();
        $object->collection[] = $object;
        self::assertEquals($this->getContent('circular_reference_collection'), $this->serialize($object));
    }

    public function testCircularReference()
    {
        $object = new CircularReferenceParent();
        self::assertEquals($this->getContent('circular_reference'), $this->serialize($object));

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('circular_reference'), get_class($object));

            $col = $this->getField($deserialized, 'collection');
            self::assertCount(2, $col);
            self::assertEquals('child1', $col[0]->getName());
            self::assertEquals('child2', $col[1]->getName());
            self::assertSame($deserialized, $col[0]->getParent());
            self::assertSame($deserialized, $col[1]->getParent());

            $col = $this->getField($deserialized, 'anotherCollection');
            self::assertCount(2, $col);
            self::assertEquals('child1', $col[0]->getName());
            self::assertEquals('child2', $col[1]->getName());
            self::assertSame($deserialized, $col[0]->getParent());
            self::assertSame($deserialized, $col[1]->getParent());
        }
    }

    public function testLifecycleCallbacks()
    {
        $object = new ObjectWithLifecycleCallbacks();
        self::assertEquals($this->getContent('lifecycle_callbacks'), $this->serialize($object));
        self::assertAttributeSame(null, 'name', $object);

        if ($this->hasDeserializer()) {
            $deserialized = $this->deserialize($this->getContent('lifecycle_callbacks'), get_class($object));
            self::assertEquals($object, $deserialized);
        }
    }

    public function testFormErrors()
    {
        $errors = [
            new FormError('This is the form error'),
            new FormError('Another error'),
        ];

        self::assertEquals($this->getContent('form_errors'), $this->serialize($errors));
    }

    public function testNestedFormErrors()
    {
        $dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();

        $formConfigBuilder = new FormConfigBuilder('foo', null, $dispatcher);
        $formConfigBuilder->setCompound(true);
        $formConfigBuilder->setDataMapper($this->getMockBuilder('Symfony\Component\Form\DataMapperInterface')->getMock());
        $fooConfig = $formConfigBuilder->getFormConfig();

        $form = new Form($fooConfig);
        $form->addError(new FormError('This is the form error'));

        $formConfigBuilder = new FormConfigBuilder('bar', null, $dispatcher);
        $barConfig = $formConfigBuilder->getFormConfig();
        $child = new Form($barConfig);
        $child->addError(new FormError('Error of the child form'));
        $form->add($child);

        self::assertEquals($this->getContent('nested_form_errors'), $this->serialize($form));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFormErrorsWithNonFormComponents()
    {
        if (!class_exists('Symfony\Component\Form\Extension\Core\Type\SubmitType')) {
            $this->markTestSkipped('Not using Symfony Form >= 2.3 with submit type');
        }

        $dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();

        $factoryBuilder = new FormFactoryBuilder();
        $factoryBuilder->addType(new SubmitType());
        $factoryBuilder->addType(new ButtonType());
        $factory = $factoryBuilder->getFormFactory();

        $formConfigBuilder = new FormConfigBuilder('foo', null, $dispatcher);
        $formConfigBuilder->setFormFactory($factory);
        $formConfigBuilder->setCompound(true);
        $formConfigBuilder->setDataMapper($this->getMockBuilder('Symfony\Component\Form\DataMapperInterface')->getMock());
        $fooConfig = $formConfigBuilder->getFormConfig();

        $form = new Form($fooConfig);
        $form->add('save', SubmitType::class);

        try {
            $this->serialize($form);
        } catch (\Throwable $e) {
            self::assertTrue(false, 'Serialization should not throw an exception');
        }
    }

    public function testConstraintViolation()
    {
        $violation = new ConstraintViolation('Message of violation', 'Message of violation', [], null, 'foo', null);

        self::assertEquals($this->getContent('constraint_violation'), $this->serialize($violation));
    }

    public function testConstraintViolationList()
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation('Message of violation', 'Message of violation', [], null, 'foo', null));
        $violations->add(new ConstraintViolation('Message of another violation', 'Message of another violation', [], null, 'bar', null));

        self::assertEquals($this->getContent('constraint_violation_list'), $this->serialize($violations));
    }

    public function testDoctrineProxy()
    {
        if (!class_exists('Doctrine\ORM\Version')) {
            $this->markTestSkipped('Doctrine is not available.');
        }

        $object = new SimpleObjectProxy('foo', 'bar');

        self::assertEquals($this->getContent('orm_proxy'), $this->serialize($object));
    }

    public function testInitializedDoctrineProxy()
    {
        if (!class_exists('Doctrine\ORM\Version')) {
            $this->markTestSkipped('Doctrine is not available.');
        }

        $object = new SimpleObjectProxy('foo', 'bar');
        $object->__load();

        self::assertEquals($this->getContent('orm_proxy'), $this->serialize($object));
    }

    public function testCustomAccessor()
    {
        $post = new IndexedCommentsBlogPost();

        self::assertEquals($this->getContent('custom_accessor'), $this->serialize($post));
    }

    public function testMixedAccessTypes()
    {
        $object = new GetSetObject();

        self::assertEquals($this->getContent('mixed_access_types'), $this->serialize($object));

        if ($this->hasDeserializer()) {
            $object = $this->deserialize($this->getContent('mixed_access_types'), 'JMS\Serializer\Tests\Fixtures\GetSetObject');
            self::assertAttributeEquals(1, 'id', $object);
            self::assertAttributeEquals('Johannes', 'name', $object);
            self::assertAttributeEquals(42, 'readOnlyProperty', $object);
        }
    }

    public function testAccessorOrder()
    {
        self::assertEquals($this->getContent('accessor_order_child'), $this->serialize(new AccessorOrderChild()));
        self::assertEquals($this->getContent('accessor_order_parent'), $this->serialize(new AccessorOrderParent()));
        self::assertEquals($this->getContent('accessor_order_methods'), $this->serialize(new AccessorOrderMethod()));
    }

    public function testGroups()
    {
        $groupsObject = new GroupsObject();

        self::assertEquals($this->getContent('groups_all'), $this->serializer->serialize($groupsObject, $this->getFormat()));

        self::assertEquals(
            $this->getContent('groups_foo'),
            $this->serializer->serialize($groupsObject, $this->getFormat(), SerializationContext::create()->setGroups(['foo']))
        );

        self::assertEquals(
            $this->getContent('groups_foobar'),
            $this->serializer->serialize($groupsObject, $this->getFormat(), SerializationContext::create()->setGroups(['foo', 'bar']))
        );

        self::assertEquals(
            $this->getContent('groups_all'),
            $this->serializer->serialize($groupsObject, $this->getFormat())
        );

        self::assertEquals(
            $this->getContent('groups_default'),
            $this->serializer->serialize($groupsObject, $this->getFormat(), SerializationContext::create()->setGroups([GroupsExclusionStrategy::DEFAULT_GROUP]))
        );

        self::assertEquals(
            $this->getContent('groups_default'),
            $this->serializer->serialize($groupsObject, $this->getFormat(), SerializationContext::create()->setGroups([GroupsExclusionStrategy::DEFAULT_GROUP]))
        );
    }

    public function testAdvancedGroups()
    {
        $adrien = new GroupsUser(
            'John',
            new GroupsUser(
                'John Manager',
                null,
                [
                    new GroupsUser(
                        'John Manager friend 1',
                        new GroupsUser('John Manager friend 1 manager')
                    ),
                    new GroupsUser('John Manager friend 2'),
                ]
            ),
            [
                new GroupsUser(
                    'John friend 1',
                    new GroupsUser('John friend 1 manager')
                ),
                new GroupsUser(
                    'John friend 2',
                    new GroupsUser('John friend 2 manager')
                ),
            ]
        );

        self::assertEquals(
            $this->getContent('groups_advanced'),
            $this->serializer->serialize(
                $adrien,
                $this->getFormat(),
                SerializationContext::create()->setGroups([
                    GroupsExclusionStrategy::DEFAULT_GROUP,
                    'manager_group',
                    'friends_group',

                    'manager' => [
                        GroupsExclusionStrategy::DEFAULT_GROUP,
                        'friends_group',

                        'friends' => ['nickname_group'],
                    ],
                    'friends' => [
                        'manager_group',
                        'nickname_group',
                    ],
                ])
            )
        );
    }

    /**
     * @expectedException \JMS\Serializer\Exception\InvalidMetadataException
     * @expectedExceptionMessage Invalid group name "foo, bar" on "JMS\Serializer\Tests\Fixtures\InvalidGroupsObject->foo", did you mean to create multiple groups?
     */
    public function testInvalidGroupName()
    {
        $groupsObject = new InvalidGroupsObject();

        $this->serializer->serialize($groupsObject, $this->getFormat());
    }

    public function testVirtualProperty()
    {
        self::assertEquals($this->getContent('virtual_properties'), $this->serialize(new ObjectWithVirtualProperties()));
    }

    public function testVirtualVersions()
    {
        self::assertEquals(
            $this->getContent('virtual_properties_low'),
            $this->serialize(new ObjectWithVersionedVirtualProperties(), SerializationContext::create()->setVersion('2'))
        );

        self::assertEquals(
            $this->getContent('virtual_properties_all'),
            $this->serialize(new ObjectWithVersionedVirtualProperties(), SerializationContext::create()->setVersion('7'))
        );

        self::assertEquals(
            $this->getContent('virtual_properties_high'),
            $this->serialize(new ObjectWithVersionedVirtualProperties(), SerializationContext::create()->setVersion('9'))
        );
    }

    public function testCustomHandler()
    {
        if (!$this->hasDeserializer()) {
            return;
        }

        $handler = static function () {
            return new CustomDeserializationObject('customly_unserialized_value');
        };

        $this->handlerRegistry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, 'CustomDeserializationObject', $this->getFormat(), $handler);

        $serialized = $this->serializer->serialize(new CustomDeserializationObject('sometext'), $this->getFormat());
        $object = $this->serializer->deserialize($serialized, 'CustomDeserializationObject', $this->getFormat());
        self::assertEquals('customly_unserialized_value', $object->someProperty);
    }

    public function testInput()
    {
        self::assertEquals($this->getContent('input'), $this->serializer->serialize(new Input(), $this->getFormat()));
    }

    public function testObjectWithEmptyHash()
    {
        self::assertEquals($this->getContent('hash_empty'), $this->serializer->serialize(new ObjectWithEmptyHash(), $this->getFormat()));
    }

    /**
     * @group null
     */
    public function testSerializeObjectWhenNull()
    {
        self::assertEquals(
            $this->getContent('object_when_null'),
            $this->serialize(new Comment(null, 'foo'), SerializationContext::create()->setSerializeNull(false))
        );

        self::assertEquals(
            $this->getContent('object_when_null_and_serialized'),
            $this->serialize(new Comment(null, 'foo'), SerializationContext::create()->setSerializeNull(true))
        );
    }

    /**
     * @group polymorphic
     */
    public function testPolymorphicObjectsWithGroup()
    {
        $context = SerializationContext::create();
        $context->setGroups(['foo']);

        self::assertEquals(
            $this->getContent('car'),
            $this->serialize(new DiscriminatorGroupCar(5), $context)
        );
    }

    /**
     * @group polymorphic
     */
    public function testPolymorphicObjects()
    {
        self::assertEquals(
            $this->getContent('car'),
            $this->serialize(new Car(5))
        );
        self::assertEquals(
            $this->getContent('post'),
            $this->serialize(new Post('Post Title'))
        );
        self::assertEquals(
            $this->getContent('image_post'),
            $this->serialize(new ImagePost('Image Post Title'))
        );

        if ($this->hasDeserializer()) {
            self::assertEquals(
                new Car(5),
                $this->deserialize(
                    $this->getContent('car'),
                    'JMS\Serializer\Tests\Fixtures\Discriminator\Car'
                ),
                'Class is resolved correctly when concrete sub-class is used.'
            );

            self::assertEquals(
                new Car(5),
                $this->deserialize(
                    $this->getContent('car'),
                    'JMS\Serializer\Tests\Fixtures\Discriminator\Vehicle'
                ),
                'Class is resolved correctly when least supertype is used.'
            );

            self::assertEquals(
                new Car(5),
                $this->deserialize(
                    $this->getContent('car_without_type'),
                    'JMS\Serializer\Tests\Fixtures\Discriminator\Car'
                ),
                'Class is resolved correctly when concrete sub-class is used and no type is defined.'
            );

            self::assertEquals(
                new Post('Post Title'),
                $this->deserialize(
                    $this->getContent('post'),
                    'JMS\Serializer\Tests\Fixtures\Discriminator\Post'
                ),
                'Class is resolved correctly when parent class is used and type is set.'
            );

            self::assertEquals(
                new ImagePost('Image Post Title'),
                $this->deserialize(
                    $this->getContent('image_post'),
                    'JMS\Serializer\Tests\Fixtures\Discriminator\Post'
                ),
                'Class is resolved correctly when least supertype is used.'
            );

            self::assertEquals(
                new ImagePost('Image Post Title'),
                $this->deserialize(
                    $this->getContent('image_post'),
                    'JMS\Serializer\Tests\Fixtures\Discriminator\ImagePost'
                ),
                'Class is resolved correctly when concrete sub-class is used and no type is defined.'
            );
        }
    }

    /**
     * @group polymorphic
     */
    public function testNestedPolymorphicObjects()
    {
        $garage = new Garage([new Car(3), new Moped(1)]);
        self::assertEquals(
            $this->getContent('garage'),
            $this->serialize($garage)
        );

        if ($this->hasDeserializer()) {
            self::assertEquals(
                $garage,
                $this->deserialize(
                    $this->getContent('garage'),
                    'JMS\Serializer\Tests\Fixtures\Garage'
                )
            );
        }
    }

    /**
     * @group polymorphic
     */
    public function testNestedPolymorphicInterfaces()
    {
        $garage = new VehicleInterfaceGarage([new Car(3), new Moped(1)]);
        self::assertEquals(
            $this->getContent('garage'),
            $this->serialize($garage)
        );

        if ($this->hasDeserializer()) {
            self::assertEquals(
                $garage,
                $this->deserialize(
                    $this->getContent('garage'),
                    'JMS\Serializer\Tests\Fixtures\VehicleInterfaceGarage'
                )
            );
        }
    }

    /**
     * @group polymorphic
     * @expectedException LogicException
     */
    public function testPolymorphicObjectsInvalidDeserialization()
    {
        if (!$this->hasDeserializer()) {
            throw new \LogicException('No deserializer');
        }

        $this->deserialize(
            $this->getContent('car_without_type'),
            'JMS\Serializer\Tests\Fixtures\Discriminator\Vehicle'
        );
    }

    public function testDepthExclusionStrategy()
    {
        $context = SerializationContext::create()
            ->addExclusionStrategy(new DepthExclusionStrategy());

        $data = new Tree(
            new Node([
                new Node([
                    new Node([
                        new Node([
                            new Node(),
                        ]),
                    ]),
                ]),
            ])
        );

        self::assertEquals($this->getContent('tree'), $this->serializer->serialize($data, $this->getFormat(), $context));
    }

    public function testMaxDepthWithSkippableObject()
    {
        $data = new Gh236Foo();

        $context = SerializationContext::create()->enableMaxDepthChecks();
        $serialized = $this->serialize($data, $context);

        self::assertEquals($this->getContent('maxdepth_skippabe_object'), $serialized);
    }

    public function testDeserializingIntoExistingObject()
    {
        if (!$this->hasDeserializer()) {
            return;
        }
        $objectConstructor = new InitializedObjectConstructor(new UnserializeObjectConstructor());

        $builder = SerializerBuilder::create();
        $builder->setObjectConstructor($objectConstructor);
        $serializer = $builder->build();

        $order = new Order(new Price(12));

        $context = new DeserializationContext();
        $context->setAttribute('target', $order);

        $deseralizedOrder = $serializer->deserialize(
            $this->getContent('order'),
            get_class($order),
            $this->getFormat(),
            $context
        );

        self::assertSame($order, $deseralizedOrder);
        self::assertEquals(new Order(new Price(12.34)), $deseralizedOrder);
        self::assertAttributeInstanceOf('JMS\Serializer\Tests\Fixtures\Price', 'cost', $deseralizedOrder);
    }

    public function testObjectWithNullableArrays()
    {
        $object = new ObjectWithEmptyNullableAndEmptyArrays();
        self::assertEquals($this->getContent('nullable_arrays'), $this->serializer->serialize($object, $this->getFormat()));
    }

    /**
     * @dataProvider getSerializeNullCases
     */
    public function testSerializeNullArrayObjectWithExclusionStrategy(bool $serializeNull)
    {
        $arr = [
            new SimpleObject('foo1', 'bar1'),
        ];

        $serializationContext = SerializationContext::create();
        $serializationContext->setSerializeNull($serializeNull);
        $serializationContext->setInitialType('array<' . SimpleObject::class . '>');
        $serializationContext->addExclusionStrategy(new AlwaysExcludeExclusionStrategy());
        self::assertEquals(
            $this->getContent('array_objects_nullable'),
            $this->serializer->serialize($arr, $this->getFormat(), $serializationContext)
        );
    }

    public function testHandlerInvokedOnPrimitives()
    {
        $invoked = false;
        $this->handlerRegistry->registerHandler(
            GraphNavigatorInterface::DIRECTION_SERIALIZATION,
            'Virtual',
            $this->getFormat(),
            function ($visitor, $data) use (&$invoked) {
                $invoked = true;
                $this->assertEquals('foo', $data);
                return null;
            }
        );

        $this->serializer->serialize('foo', $this->getFormat(), null, 'Virtual');
        $this->assertTrue($invoked);
    }

    public function getFirstClassListCollectionsValues()
    {
        $collection = new FirstClassListCollection([1, 2]);

        return [
            [[1, 2, 3], $this->getContent('inline_list_collection')],
            [[], $this->getContent('inline_empty_list_collection')],
            [[1, 'a' => 2], $this->getContent('inline_deserialization_list_collection'), $collection],
        ];
    }

    /**
     * @param array $items
     * @param array $expected
     *
     * @dataProvider getFirstClassListCollectionsValues
     */
    public function testFirstClassListCollections($items, $expected, ?FirstClassListCollection $expectedDeserializatrion = null)
    {
        $collection = new FirstClassListCollection($items);

        self::assertSame($expected, $this->serialize($collection));
        self::assertEquals(
            $expectedDeserializatrion ?: $collection,
            $this->deserialize($expected, get_class($collection))
        );
    }

    public function testInlineCollection()
    {
        $list = new AuthorsInline(new Author('foo'), new Author('bar'));
        self::assertEquals($this->getContent('authors_inline'), $this->serialize($list));
        self::assertEquals($list, $this->deserialize($this->getContent('authors_inline'), AuthorsInline::class));
    }

    public function getSerializeNullCases()
    {
        return [
            [true],
            [false],
        ];
    }

    abstract protected function getContent($key);

    abstract protected function getFormat();

    protected function hasDeserializer()
    {
        return true;
    }

    protected function serialize($data, ?Context $context = null)
    {
        return $this->serializer->serialize($data, $this->getFormat(), $context);
    }

    protected function deserialize($content, $type, ?Context $context = null)
    {
        return $this->serializer->deserialize($content, $type, $this->getFormat(), $context);
    }

    protected function setUp()
    {
        $this->handlerRegistry = new HandlerRegistry();
        $this->handlerRegistry->registerSubscribingHandler(new ConstraintViolationHandler());
        $this->handlerRegistry->registerSubscribingHandler(new StdClassHandler());
        $this->handlerRegistry->registerSubscribingHandler(new DateHandler());
        $this->handlerRegistry->registerSubscribingHandler(new FormErrorHandler(new IdentityTranslator(new MessageSelector())));
        $this->handlerRegistry->registerSubscribingHandler(new ArrayCollectionHandler());
        $this->handlerRegistry->registerHandler(
            GraphNavigatorInterface::DIRECTION_SERIALIZATION,
            'AuthorList',
            $this->getFormat(),
            static function (SerializationVisitorInterface $visitor, $object, array $type, Context $context) {
                return $visitor->visitArray(iterator_to_array($object), $type);
            }
        );
        $this->handlerRegistry->registerHandler(
            GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
            'AuthorList',
            $this->getFormat(),
            static function (DeserializationVisitorInterface $visitor, $data, $type, Context $context) {
                $type = [
                    'name' => 'array',
                    'params' => [
                        ['name' => 'integer', 'params' => []],
                        ['name' => 'JMS\Serializer\Tests\Fixtures\Author', 'params' => []],
                    ],
                ];

                $elements = $context->getNavigator()->accept($data, $type);
                $list = new AuthorList();
                foreach ($elements as $author) {
                    $list->add($author);
                }

                return $list;
            }
        );

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(new DoctrineProxySubscriber());

        $builder = SerializerBuilder::create($this->handlerRegistry, $this->dispatcher);
        $this->serializer = $builder->build();
    }

    protected function getField($obj, $name)
    {
        $ref = new \ReflectionProperty($obj, $name);
        $ref->setAccessible(true);

        return $ref->getValue($obj);
    }

    private function setField($obj, $name, $value)
    {
        $ref = new \ReflectionProperty($obj, $name);
        $ref->setAccessible(true);
        $ref->setValue($obj, $value);
    }
}
