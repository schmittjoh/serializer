<?php

declare(strict_types=1);

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer;

use JMS\Serializer\Exception\RuntimeException;
use Metadata\MetadataFactoryInterface;

class SerializationContext extends Context
{
    /** @var \SplObjectStorage */
    private $visitingSet;

    /** @var \SplStack */
    private $visitingStack;

    /**
     * @var string
     */
    private $initialType;

    public static function create()
    {
        return new self();
    }

    /**
     * @param string $format
     */
    public function initialize(string $format, $visitor, GraphNavigatorInterface $navigator, MetadataFactoryInterface $factory): void
    {
        parent::initialize($format, $visitor, $navigator, $factory);

        $this->visitingSet = new \SplObjectStorage();
        $this->visitingStack = new \SplStack();
    }

    public function startVisiting($object): void
    {
        if (!\is_object($object)) {
            return;
        }
        $this->visitingSet->attach($object);
        $this->visitingStack->push($object);
    }

    public function stopVisiting($object): void
    {
        if (!\is_object($object)) {
            return;
        }
        $this->visitingSet->detach($object);

        if ($object !== $this->visitingStack->pop()) {
            throw new RuntimeException('Context visitingStack not working well');
        }
    }

    public function isVisiting($object): bool
    {
        if (!\is_object($object)) {
            return false;
        }

        return $this->visitingSet->contains($object);
    }

    public function getPath(): string
    {
        $path = array();
        foreach ($this->visitingStack as $obj) {
            $path[] = \get_class($obj);
        }

        if (!$path) {
            return null;
        }

        return implode(' -> ', $path);
    }

    public function getDirection(): int
    {
        return GraphNavigatorInterface::DIRECTION_SERIALIZATION;
    }

    public function getDepth(): int
    {
        return $this->visitingStack->count();
    }

    public function getObject()
    {
        return !$this->visitingStack->isEmpty() ? $this->visitingStack->top() : null;
    }

    public function getVisitingStack()
    {
        return $this->visitingStack;
    }

    public function getVisitingSet()
    {
        return $this->visitingSet;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setInitialType($type): self
    {
        $this->initialType = $type;
        $this->setAttribute('initial_type', $type);
        return $this;
    }

    public function getInitialType(): ?string
    {
        return $this->initialType
            ? $this->initialType
            : $this->hasAttribute('initial_type') ? $this->getAttribute('initial_type') : null;
    }
}
