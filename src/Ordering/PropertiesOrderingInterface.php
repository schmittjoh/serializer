<?php

declare(strict_types=1);

namespace JMS\Serializer\Ordering;

use JMS\Serializer\Metadata\PropertyMetadata;

interface PropertiesOrderingInterface
{
    /**
     * @param PropertyMetadata[] $properties name => property
     * @param array $options    name => options
     *
     * @return PropertyMetadata[] name => property
     */
    public function order(array $properties, array $options): array;
}
