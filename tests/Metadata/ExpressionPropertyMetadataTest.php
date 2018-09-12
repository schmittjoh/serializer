<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata;

use JMS\Serializer\Metadata\ExpressionPropertyMetadata;

class ExpressionPropertyMetadataTest extends AbstractPropertyMetadataTest
{
    public function testSerialization()
    {
        $meta = new ExpressionPropertyMetadata('stdClass', 'foo', 'fooExpr');
        $this->setNonDefaultMetadataValues($meta);

        $restoredMeta = unserialize(serialize($meta));
        $this->assertEquals($meta, $restoredMeta);
    }
}
