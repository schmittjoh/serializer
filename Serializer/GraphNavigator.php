<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Serializer;

use JMS\SerializerBundle\Metadata\ClassMetadata;
use Metadata\MetadataFactoryInterface;
use JMS\SerializerBundle\Exception\InvalidArgumentException;
use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyInterface;

final class GraphNavigator
{
    const DIRECTION_SERIALIZATION = 1;
    const DIRECTION_DESERIALIZATION = 2;

    private $direction;
    private $exclusionStrategy;
    private $metadataFactory;
    private $visiting;

    public function __construct($direction, MetadataFactoryInterface $metadataFactory, ExclusionStrategyInterface $exclusionStrategy = null)
    {
        $this->direction = $direction;
        $this->metadataFactory = $metadataFactory;
        $this->exclusionStrategy = $exclusionStrategy;
        $this->visiting = new \SplObjectStorage();
    }

    public function accept($data, $type, VisitorInterface $visitor)
    {
        // determine type if not given
        if (null === $type) {
            if (null === $data) {
                return null;
            }

            $type = gettype($data);
            if ('object' === $type) {
                $type = get_class($data);
            }
        }

        if ('string' === $type) {
            return $visitor->visitString($data, $type);
        } else if ('integer' === $type) {
            return $visitor->visitInteger($data, $type);
        } else if ('boolean' === $type) {
            return $visitor->visitBoolean($data, $type);
        } else if ('double' === $type) {
            return $visitor->visitDouble($data, $type);
        } else if ('array' === $type || ('a' === $type[0] && 0 === strpos($type, 'array<'))) {
            return $visitor->visitArray($data, $type);
        } else if ('resource' === $type) {
            $path = array();
            foreach ($this->visiting as $obj) {
                $path[] = get_class($obj);
            }

            $msg = 'Resources are not supported in serialized data.';
            if ($path) {
                $msg .= ' Path: '.implode(' -> ', $path);
            }

            throw new \RuntimeException($msg);
        } else {
            if (self::DIRECTION_SERIALIZATION === $this->direction && null !== $data) {
                if ($this->visiting->contains($data)) {
                    return null;
                }
                $this->visiting->attach($data);
            }

            // try custom handler
            $handled = false;
            $rs = $visitor->visitUsingCustomHandler($data, $type, $handled);
            if ($handled) {
                if (self::DIRECTION_SERIALIZATION === $this->direction) {
                    $this->visiting->detach($data);
                }

                return $rs;
            }

            $metadata = $this->metadataFactory->getMetadataForClass($type);
            if (null !== $this->exclusionStrategy && $this->exclusionStrategy->shouldSkipClass($metadata)) {
                if (self::DIRECTION_SERIALIZATION === $this->direction) {
                    $this->visiting->detach($data);
                }

                return null;
            }

            // pre-serialization callbacks
            if (self::DIRECTION_SERIALIZATION === $this->direction) {
                foreach ($metadata->preSerializeMethods as $method) {
                    $method->invoke($data);
                }
            }

            // check if traversable
            if (self::DIRECTION_SERIALIZATION === $this->direction && $data instanceof \Traversable) {
                $rs = $visitor->visitTraversable($data, $type);
                $this->afterVisitingObject($metadata, $data, self::DIRECTION_SERIALIZATION === $this->direction);

                return $rs;
            }

            $visitor->startVisitingObject($metadata, $data, $type);
            foreach ($metadata->propertyMetadata as $propertyMetadata) {
                if (null !== $this->exclusionStrategy && $this->exclusionStrategy->shouldSkipProperty($propertyMetadata)) {
                    continue;
                }

                if (self::DIRECTION_DESERIALIZATION === $this->direction && $propertyMetadata->readOnly) {
                    continue;
                }

                // try custom handler
                if (!$visitor->visitPropertyUsingCustomHandler($propertyMetadata, $data)) {
                    $visitor->visitProperty($propertyMetadata, $data);
                }
            }

            $rs = $visitor->endVisitingObject($metadata, $data, $type);
            $this->afterVisitingObject($metadata, self::DIRECTION_SERIALIZATION === $this->direction ? $data : $rs);

            return $rs;
        }
    }

    public function detachObject($object)
    {
        if (null === $object) {
            throw new InvalidArgumentException('$object cannot be null');
        } else if (!is_object($object)) {
            throw new InvalidArgumentException(sprintf('Expected an object to detach, given "%s".', gettype($object)));
        }

        $this->visiting->detach($object);
    }

    private function afterVisitingObject(ClassMetadata $metadata, $object)
    {
        if (self::DIRECTION_SERIALIZATION === $this->direction) {
            $this->visiting->detach($object);

            foreach ($metadata->postSerializeMethods as $method) {
                $method->invoke($object);
            }

            return;
        }

        foreach ($metadata->postDeserializeMethods as $method) {
            $method->invoke($object);
        }
    }
}
