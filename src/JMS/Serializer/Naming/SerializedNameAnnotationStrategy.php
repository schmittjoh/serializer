<?php

namespace JMS\Serializer\Naming;

use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Naming strategy which uses an annotation to translate the property name.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class SerializedNameAnnotationStrategy implements PropertyNamingStrategyInterface
{
    private $delegate;

    public function __construct(PropertyNamingStrategyInterface $namingStrategy)
    {
        $this->delegate = $namingStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function translateName(PropertyMetadata $property)
    {
        if (null !== $name = $property->serializedName) {
            return $name;
        }

        return $this->delegate->translateName($property);
    }
}
