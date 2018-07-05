<?php

declare(strict_types=1);

namespace JMS\Serializer\Naming;

use JMS\Serializer\Metadata\PropertyMetadata;
use function preg_replace;
use function strtolower;
use function ucfirst;

/**
 * Generic naming strategy which translates a camel-cased property name.
 */
final class CamelCaseNamingStrategy implements PropertyNamingStrategyInterface
{
    private $separator;
    private $lowerCase;

    public function __construct(string $separator = '_', bool $lowerCase = true)
    {
        $this->separator = $separator;
        $this->lowerCase = $lowerCase;
    }

    /**
     * {@inheritDoc}
     */
    public function translateName(PropertyMetadata $property): string
    {
        $name = preg_replace('/[A-Z]+/', $this->separator . '\\0', $property->name);

        if ($this->lowerCase) {
            return strtolower($name);
        }

        return ucfirst($name);
    }
}
