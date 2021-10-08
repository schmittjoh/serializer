<?php

declare(strict_types=1);

namespace JMS\Serializer;

use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Exception\LogicException;

class DeserializationContext extends Context
{
    /**
     * @var int
     */
    private $depth = 0;

    /**
     * @var array<string, PersistentCollection>
     */
    private $persistentCollections = [];

    public static function create(): self
    {
        return new self();
    }

    public function getDirection(): int
    {
        return GraphNavigatorInterface::DIRECTION_DESERIALIZATION;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function increaseDepth(): void
    {
        $this->depth += 1;
    }

    public function decreaseDepth(): void
    {
        if ($this->depth <= 0) {
            throw new LogicException('Depth cannot be smaller than zero.');
        }

        $this->depth -= 1;
    }

    public function addPersistentCollection(PersistentCollection $collection, array $path): void
    {
        $this->persistentCollections[implode('.', $path)] = $collection;
    }

    public function removePersistentCollectionForCurrentPath(): ?PersistentCollection
    {
        $path = implode('.', $this->getCurrentPath());
        if (isset($this->persistentCollections[$path])) {
            $return = $this->persistentCollections[$path];
            unset($this->persistentCollections[$path]);

            return $return;
        }

        return null;
    }
}
