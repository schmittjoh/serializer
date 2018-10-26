<?php

declare(strict_types=1);

namespace JMS\Serializer\Naming;

use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Interface for property naming strategies.
 *
 * Implementations translate the property name to a serialized name that is
 * displayed.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface PropertyNamingStrategyInterface
{
    /**
     * Translates the name of the property to the serialized version.
     */
    public function translateName(PropertyMetadata $property): string;
}
