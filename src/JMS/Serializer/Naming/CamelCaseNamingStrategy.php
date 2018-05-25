<?php

namespace JMS\Serializer\Naming;

use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Generic naming strategy which translates a camel-cased property name.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class CamelCaseNamingStrategy implements PropertyNamingStrategyInterface
{
    private $separator;
    private $lowerCase;

    public function __construct($separator = '_', $lowerCase = true)
    {
        $this->separator = $separator;
        $this->lowerCase = $lowerCase;
    }

    /**
     * {@inheritDoc}
     */
    public function translateName(PropertyMetadata $property)
    {
        $name = preg_replace('/[A-Z]/', $this->separator . '\\0', $property->name);

        if ($this->lowerCase) {
            return strtolower($name);
        }

        return ucfirst($name);
    }
}
