<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Visitor\Factory\JsonDeserializationVisitorFactory;

class JsonStrictSerializationTest extends JsonSerializationTest
{
    protected function extendBuilder(SerializerBuilder $builder): void
    {
        parent::extendBuilder($builder);

        $builder->setDeserializationVisitor('json', new JsonDeserializationVisitorFactory(true));
    }

    /**
     * @param array $items
     * @param array $expected
     *
     * @dataProvider getFirstClassMapCollectionsValues
     */
    public function testFirstClassMapCollections($items, $expected): void
    {
        self::markTestIncomplete('Fixtures are broken');
    }
}
