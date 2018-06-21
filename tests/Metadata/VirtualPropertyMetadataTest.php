<?php

namespace JMS\Serializer\Tests\Metadata;

use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Tests\Fixtures\ObjectWithVirtualProperties;

class VirtualPropertyMetadataTest extends AbstractPropertyMetadataTest
{
    public function testSerialization()
    {
        $meta = new VirtualPropertyMetadata(ObjectWithVirtualProperties::class, 'getEmptyValue');
        $this->setNonDefaultMetadataValues($meta);

        $restoredMeta = unserialize(serialize($meta));
        $this->assertEquals($meta, $restoredMeta);
    }

}
