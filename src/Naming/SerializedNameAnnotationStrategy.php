<?php

declare(strict_types=1);

namespace JMS\Serializer\Naming;

use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Naming strategy which uses an annotation to translate the property name.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class SerializedNameAnnotationStrategy implements PropertyNamingStrategyInterface
{
    /**
     * @var PropertyNamingStrategyInterface
     */
    private $delegate;

    public function __construct(PropertyNamingStrategyInterface $namingStrategy)
    {
        $this->delegate = $namingStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function translateName(PropertyMetadata $property): string
    {
        if (null !== $name = $property->serializedName) {
            return $name;
        }

        return $this->delegate->translateName($property);
    }
}
