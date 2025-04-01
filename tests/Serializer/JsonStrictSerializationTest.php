<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Exception\NonVisitableTypeException;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Visitor\Factory\JsonDeserializationVisitorFactory;
use PHPUnit\Framework\Attributes\DataProvider;

class JsonStrictSerializationTest extends JsonSerializationTest
{
    protected function extendBuilder(SerializerBuilder $builder): void
    {
        parent::extendBuilder($builder);

        $builder->setDeserializationVisitor('json', new JsonDeserializationVisitorFactory(true));
    }

    /**
     * @dataProvider getFirstClassMapCollectionsValues
     */
    #[DataProvider('getFirstClassMapCollectionsValues')]
    public function testFirstClassMapCollections(array $items, string $expected): void
    {
        self::markTestIncomplete('Fixtures are broken');
    }

    /**
     * @dataProvider getUnionCastableTypes
     */
    #[DataProvider('getUnionCastableTypes')]
    public function testUnionPropertiesWithCastableType($data, string $expected): void
    {
        $this->expectException(NonVisitableTypeException::class);

        parent::testUnionPropertiesWithCastableType($data, $expected);
    }
}
