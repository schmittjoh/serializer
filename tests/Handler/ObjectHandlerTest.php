<?php

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\ObjectHandler;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\Handler\DynamicObject;
use JMS\Serializer\Tests\Fixtures\Handler\ObjectFixture;

class ObjectHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function setUp()
    {
        $this->serializer  = SerializerBuilder::create()
            ->configureHandlers(function (HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new ObjectHandler());
            })
            ->addDefaultHandlers()
            ->build();
    }

    public function testSerializeObject()
    {
        $result = $this->serializer->toArray((new ObjectFixture())->setProperties());

        self::assertEquals(
            ['id' => 3, 'object' => ['string' => 'hello', '::class' => DynamicObject::class]],
            $result
        );
    }

    public function testDeSerializeObject()
    {
        $result = $this->serializer->fromArray(
            ['id' => 3, 'object' => ['string' => 'hello', '::class' => DynamicObject::class]],
            ObjectFixture::class
        );

        self::assertEquals(
            (new ObjectFixture())->setProperties(),
            $result
        );
    }
}
