<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata;

use JMS\Serializer\Metadata\ExpressionPropertyMetadata;

class ExpressionPropertyMetadataTest extends AbstractPropertyMetadataTestCase
{
    public function testSerialization()
    {
        $meta = new ExpressionPropertyMetadata('stdClass', 'foo', 'fooExpr');
        $this->setNonDefaultMetadataValues($meta);

        $restoredMeta = unserialize(serialize($meta));
        self::assertEquals($meta, $restoredMeta);
    }
}
