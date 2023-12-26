<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Tests\Fixtures\SimpleObject;

class PropertyMetadataTest extends AbstractPropertyMetadataTestCase
{
    public function testSerialization()
    {
        $meta = new PropertyMetadata(SimpleObject::class, 'foo');
        $this->setNonDefaultMetadataValues($meta);
        $meta->getter = 'getFoo';
        $meta->setter = 'setFoo';
        $meta->readOnly = true;

        $restoredMeta = unserialize(serialize($meta));
        self::assertEquals($meta, $restoredMeta);
    }
}
