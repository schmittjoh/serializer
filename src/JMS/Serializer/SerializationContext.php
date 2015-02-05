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

    /** @var int */
    protected $maxRecursionDepth = 0;

    /** @var \SplObjectStorage */
    private $visitingSet;

    /** @var \SplStack */
    private $visitingStack;

    public static function create()
    {
        return new self();
    }

    /**
     * @param string                   $format
     * @param VisitorInterface         $visitor
     * @param GraphNavigator           $navigator
     * @param MetadataFactoryInterface $factory
     */
    public function initialize(
        $format,
        VisitorInterface $visitor,
        GraphNavigator $navigator,
        MetadataFactoryInterface $factory
    ) {
        parent::initialize($format, $visitor, $navigator, $factory);

        $this->visitingSet = new \SplObjectStorage();
        $this->visitingStack = new \SplStack();
    }

    /**
     * @param $object
     */
    public function startVisiting($object)
    {
        $this->visitingSet->attach($object);
        $this->visitingStack->push($object);
    }

    /**
     * @param $object
     * @throws RuntimeException
     */
    public function stopVisiting($object)
    {
        $this->visitingSet->detach($object);
        $poppedObject = $this->visitingStack->pop();

        if ($object !== $poppedObject) {
            throw new RuntimeException('Context visitingStack not working well');
        }
    }

    /**
     * @param $object
     * @return bool
     */
    public function isVisiting($object)
    {
        if (false ===  is_object($object)) {
            throw new LogicException(
                'Expected object but got ' .
                gettype($object) .
                '. Do you have the wrong @Type mapping or could this be a Doctrine many-to-many relation?'
            );
        }

        $isVisiting = $this->visitingSet->contains($object);

        if ($isVisiting && $this->maxRecursionDepth != 0) {
            return ($this->visitingStack->count() > $this->maxRecursionDepth);
        }

        return $isVisiting;
    }

    /**
     * @return null|string
     */
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

    /**
     * @return int
     */
    public function getDirection()
    {
        return GraphNavigator::DIRECTION_SERIALIZATION;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->visitingStack->count();
    }

    /**
     * @return mixed|null
     */
    public function getObject()
    {
        return (false === $this->visitingStack->isEmpty()) ? $this->visitingStack->top() : null;
    }

    /**
     * @return \SplStack
     */
    public function getVisitingStack()
    {
        return $this->visitingStack;
    }

    /**
     * @return \SplObjectStorage
     */
    public function getVisitingSet()
    {
        return $this->visitingSet;
    }

    /**
     * @param int $maxRecursionDepth
     * @return SerializationContext
     */
    public function setMaxRecursionDepth($maxRecursionDepth = 1)
    {
        $this->maxRecursionDepth = $maxRecursionDepth;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxRecursionDepth()
    {
        return $this->maxRecursionDepth;
    }
}
