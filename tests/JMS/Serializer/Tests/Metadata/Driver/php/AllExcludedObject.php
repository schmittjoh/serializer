<?php

use JMS\Serializer\Metadata\ClassMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\AllExcludedObject');
$metadata->exclusionPolicy = 'ALL';

return $metadata;
