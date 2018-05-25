<?php

namespace JMS\Serializer\Naming;

use JMS\Serializer\Metadata\PropertyMetadata;

class CacheNamingStrategy implements PropertyNamingStrategyInterface
{
    private $delegate;
    private $cache;

    public function __construct(PropertyNamingStrategyInterface $delegate)
    {
        $this->delegate = $delegate;
        $this->cache = new \SplObjectStorage();
    }

    public function translateName(PropertyMetadata $property)
    {
        if (isset($this->cache[$property])) {
            return $this->cache[$property];
        }

        return $this->cache[$property] = $this->delegate->translateName($property);
    }
}
