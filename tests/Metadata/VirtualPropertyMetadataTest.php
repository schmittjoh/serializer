<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata;

use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Tests\Fixtures\ObjectWithVirtualProperties;

class VirtualPropertyMetadataTest extends AbstractPropertyMetadataTestCase
{
    public function testSerialization()
    {
        $meta = new VirtualPropertyMetadata(ObjectWithVirtualProperties::class, 'getEmptyValue');
        $this->setNonDefaultMetadataValues($meta);

        $restoredMeta = unserialize(serialize($meta));
        self::assertEquals($meta, $restoredMeta);
    }
}
