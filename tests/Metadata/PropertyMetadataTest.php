<?php

namespace JMS\Serializer\Tests\Metadata;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Tests\Fixtures\SimpleObject;

class PropertyMetadataTest extends AbstractPropertyMetadataTest
{
    public function testSerialization()
    {
        $meta = new PropertyMetadata(SimpleObject::class, 'foo');
        $this->setNonDefaultMetadataValues($meta);
        $meta->getter = 'getFoo';
        $meta->setter = 'setFoo';
        $meta->readOnly = true;

        $restoredMeta = unserialize(serialize($meta));
        $this->assertEquals($meta, $restoredMeta);
    }

}
