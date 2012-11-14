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

use JMS\SerializerBundle\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\SerializerBundle\Serializer\Construction\ObjectConstructorInterface;
use JMS\SerializerBundle\Serializer\Handler\HandlerRegistryInterface;
use JMS\SerializerBundle\Serializer\EventDispatcher\Event;
use JMS\SerializerBundle\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\SerializerBundle\Metadata\ClassMetadata;
use Metadata\MetadataFactoryInterface;
use JMS\SerializerBundle\Exception\InvalidArgumentException;
use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyInterface;

/**
 * Handles traversal along the object graph.
 *
 * This class handles traversal along the graph, and calls different methods
 * on visitors, or custom handlers to process its nodes.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class GraphNavigator
{
    const DIRECTION_SERIALIZATION = 1;
    const DIRECTION_DESERIALIZATION = 2;

    private $direction;
    private $dispatcher;
    private $metadataFactory;
    private $format;
    private $handlerRegistry;
    private $objectConstructor;
    private $exclusionStrategy;
    private $customHandlers = array();
    private $visiting;

    /**
     * Parses a direction string to one of the direction constants.
     *
     * @param string $dirStr
     *
     * @return integer
     */
    public static function parseDirection($dirStr)
    {
        switch (strtolower($dirStr)) {
            case 'serialization':
                return self::DIRECTION_SERIALIZATION;

            case 'deserialization':
                return self::DIRECTION_DESERIALIZATION;

            default:
                throw new \InvalidArgumentException(sprintf('The direction "%s" does not exist.', $dirStr));
        }
    }

    public function __construct($direction, MetadataFactoryInterface $metadataFactory, $format, HandlerRegistryInterface $handlerRegistry, ObjectConstructorInterface $objectConstructor, ExclusionStrategyInterface $exclusionStrategy = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->direction = $direction;
        $this->dispatcher = $dispatcher;
        $this->metadataFactory = $metadataFactory;
        $this->format = $format;
        $this->handlerRegistry = $handlerRegistry;
        $this->objectConstructor = $objectConstructor;
        $this->exclusionStrategy = $exclusionStrategy;
        $this->visiting = new \SplObjectStorage();
    }

    /**
     * Called for each node of the graph that is being traversed.
     *
     * @param mixed $data the data depends on the direction, and type of visitor
     * @param null|array $type array has the format ["name" => string, "params" => array]
     * @param VisitorInterface $visitor
     *
     * @return mixed the return value depends on the direction, and type of visitor
     */
    public function accept($data, array $type = null, VisitorInterface $visitor)
    {
        // If the type was not given, we infer the most specific type from the
        // input data in serialization mode.
        if (null === $type) {
            if (self::DIRECTION_DESERIALIZATION === $this->direction) {
                $msg = 'The type must be given for all properties when deserializing.';
                if (null !== $path = $this->getCurrentPath()) {
                    $msg .= ' Path: '.$path;
                }

                throw new \RuntimeException($msg);
            }

            $typeName = gettype($data);
            if ('object' === $typeName) {
                $typeName = get_class($data);
            }

            $type = array('name' => $typeName, 'params' => array());
        }

        switch ($type['name']) {
            case 'NULL':
                return $visitor->visitNull($data, $type);

            case 'string':
                return $visitor->visitString($data, $type);

            case 'integer':
                return $visitor->visitInteger($data, $type);

            case 'boolean':
                return $visitor->visitBoolean($data, $type);

            case 'double':
                return $visitor->visitDouble($data, $type);

            case 'array':
                return $visitor->visitArray($data, $type);

            case 'resource':
                $msg = 'Resources are not supported in serialized data.';
                if (null !== $path = $this->getCurrentPath()) {
                    $msg .= ' Path: '.implode(' -> ', $path);
                }

                throw new \RuntimeException($msg);

            default:
                $isSerializing = self::DIRECTION_SERIALIZATION === $this->direction;

                if ($isSerializing && null !== $data) {
                    if ($this->visiting->contains($data)) {
                        return null;
                    }
                    $this->visiting->attach($data);
                }

                // First, try whether a custom handler exists for the given type. This is done
                // before loading metadata because the type name might not be a class, but
                // could also simply be an artifical type.
                if (null !== $handler = $this->handlerRegistry->getHandler($this->direction, $type['name'], $this->format)) {
                    $rs = call_user_func($handler, $visitor, $data, $type);

                    if ($isSerializing) {
                        $this->visiting->detach($data);
                    }

                    return $rs;
                }

                // Trigger pre-serialization callbacks, and listeners if they exist.
                if ($isSerializing) {
                    if (null !== $this->dispatcher && $this->dispatcher->hasListeners('serializer.pre_serialize', $type['name'], $this->format)) {
                        $this->dispatcher->dispatch('serializer.pre_serialize', $type['name'], $this->format, $event = new PreSerializeEvent($visitor, $data, $type));
                        $type = $event->getType();
                    }
                }

                // Load metadata, and check whether this class should be excluded.
                $metadata = $this->metadataFactory->getMetadataForClass($type['name']);
                if (null !== $this->exclusionStrategy && $this->exclusionStrategy->shouldSkipClass($metadata, $isSerializing ? $data : null)) {
                    if ($isSerializing) {
                        $this->visiting->detach($data);
                    }

                    return null;
                }

                if ($isSerializing) {
                    foreach ($metadata->preSerializeMethods as $method) {
                        $method->invoke($data);
                    }
                }

                $object = $data;
                if ( ! $isSerializing) {
                    $object = $this->objectConstructor->construct($visitor, $metadata, $data, $type);
                }

                if (isset($metadata->handlerCallbacks[$this->direction][$this->format])) {
                    $rs = $object->{$metadata->handlerCallbacks[$this->direction][$this->format]}($visitor, $isSerializing ? null : $data);
                    $this->afterVisitingObject($visitor, $metadata, $object, $type);

                    return $isSerializing ? $rs : $object;
                }

                $visitor->startVisitingObject($metadata, $object, $type);
                foreach ($metadata->propertyMetadata as $propertyMetadata) {
                    if (null !== $this->exclusionStrategy && $this->exclusionStrategy->shouldSkipProperty($propertyMetadata, $isSerializing ? $data : null)) {
                        continue;
                    }

                    if ( ! $isSerializing && $propertyMetadata->readOnly) {
                        continue;
                    }

                    $visitor->visitProperty($propertyMetadata, $data);
                }

                if ($isSerializing) {
                    $this->afterVisitingObject($visitor, $metadata, $data, $type);

                    return $visitor->endVisitingObject($metadata, $data, $type);
                }

                $rs = $visitor->endVisitingObject($metadata, $data, $type);
                $this->afterVisitingObject($visitor, $metadata, $rs, $type);

                return $rs;
        }
    }

    /**
     * Detaches an object from the visiting map.
     *
     * Use this method if you like to re-visit an object which is already
     * being visited. Be aware that you might cause an endless loop if you
     * use this inappropriately.
     *
     * @param object $object
     */
    public function detachObject($object)
    {
        if (null === $object) {
            throw new InvalidArgumentException('$object cannot be null');
        } elseif (!is_object($object)) {
            throw new InvalidArgumentException(sprintf('Expected an object to detach, given "%s".', gettype($object)));
        }

        $this->visiting->detach($object);
    }

    private function getCurrentPath()
    {
        $path = array();
        foreach ($this->visiting as $obj) {
            $path[] = get_class($obj);
        }

        if ( ! $path) {
            return null;
        }

        return implode(' -> ', $path);
    }

    private function afterVisitingObject(VisitorInterface $visitor, ClassMetadata $metadata, $object, array $type)
    {
        if (self::DIRECTION_SERIALIZATION === $this->direction) {
            $this->visiting->detach($object);

            foreach ($metadata->postSerializeMethods as $method) {
                $method->invoke($object);
            }

            if (null !== $this->dispatcher && $this->dispatcher->hasListeners('serializer.post_serialize', $metadata->name, $this->format)) {
                $this->dispatcher->dispatch('serializer.post_serialize', $metadata->name, $this->format, new Event($visitor, $object, $type));
            }

            return;
        }

        foreach ($metadata->postDeserializeMethods as $method) {
            $method->invoke($object);
        }

        if (null !== $this->dispatcher && $this->dispatcher->hasListeners('serializer.post_deserialize', $metadata->name, $this->format)) {
            $this->dispatcher->dispatch('serializer.post_deserialize', $metadata->name, $this->format, new Event($visitor, $object, $type));
        }
    }
}
