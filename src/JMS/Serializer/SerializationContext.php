<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\RuntimeException;
use Metadata\MetadataFactoryInterface;

class SerializationContext extends Context
{
    /** @var \SplObjectStorage */
    private $visitingSet;

    /** @var \SplStack */
    private $visitingStack;

    public static function create()
    {
        return new self();
    }

    /**
     * @param string $format
     */
    public function initialize($format, VisitorInterface $visitor, GraphNavigator $navigator, MetadataFactoryInterface $factory)
    {
        parent::initialize($format, $visitor, $navigator, $factory);

        $this->visitingSet = new \SplObjectStorage();
        $this->visitingStack = new \SplStack();
    }

    public function startVisiting($object)
    {
        if ( ! is_object($object)) {
            return;
        }
        $this->visitingSet->attach($object);
        $this->visitingStack->push($object);
    }

    public function stopVisiting($object)
    {
        if ( ! is_object($object)) {
            return;
        }
        $this->visitingSet->detach($object);
        $poppedObject = $this->visitingStack->pop();

        if ($object !== $poppedObject) {
            throw new RuntimeException('Context visitingStack not working well');
        }
    }

    public function isVisiting($object)
    {
        if ( ! is_object($object)) {
            return false;
        }

        return $this->visitingSet->contains($object);
    }

    public function getPath()
    {
        $path = array();
        foreach ($this->visitingStack as $obj) {
            $path[] = get_class($obj);
        }

        if ( ! $path) {
            return null;
        }

        return implode(' -> ', $path);
    }

    public function getDirection()
    {
        return GraphNavigator::DIRECTION_SERIALIZATION;
    }

    public function getDepth()
    {
        return $this->visitingStack->count();
    }

    public function getObject()
    {
        return ! $this->visitingStack->isEmpty() ? $this->visitingStack->top() : null;
    }

    public function getVisitingStack()
    {
        return $this->visitingStack;
    }

    public function getVisitingSet()
    {
        return $this->visitingSet;
    }
}
