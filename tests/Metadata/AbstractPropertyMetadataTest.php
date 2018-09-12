<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata;

use PHPUnit\Framework\TestCase;

abstract class AbstractPropertyMetadataTest extends TestCase
{
    protected function setNonDefaultMetadataValues($metadata)
    {
        $metadata->sinceVersion = '1';
        $metadata->untilVersion = '2';
        $metadata->groups = ['test_group', 'test_group_2'];
        $metadata->serializedName = 'test_value';
        $metadata->type = 'array';
        $metadata->xmlCollection = true;
        $metadata->xmlCollectionInline = true;
        $metadata->xmlCollectionSkipWhenEmpty = false;
        $metadata->xmlEntryName = 'test_xml_entry_name';
        $metadata->xmlEntryNamespace = 'test_xml_entry_namespace';
        $metadata->xmlKeyAttribute = 'test_xml_key_attribute';
        $metadata->xmlAttribute = true;
        $metadata->xmlValue = true;
        $metadata->xmlNamespace = 'test_xml_namespace';
        $metadata->xmlKeyValuePairs = true;
        $metadata->xmlElementCData = false;
        $metadata->inline = true;
        $metadata->skipWhenEmpty = true;
        $metadata->xmlAttributeMap = true;
        $metadata->maxDepth = 1;
        $metadata->excludeIf = 'expr';
    }
}
