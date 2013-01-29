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

namespace JMS\Serializer;

use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\EventDispatcher\Event;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use Metadata\MetadataFactoryInterface;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;

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

    private $context;
    private $dispatcher;
    private $metadataFactory;
    private $handlerRegistry;
    private $objectConstructor;
    private $exclusionStrategy;

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
                throw new InvalidArgumentException(sprintf('The direction "%s" does not exist.', $dirStr));
        }
    }

    public function __construct($direction, MetadataFactoryInterface $metadataFactory, $format, HandlerRegistryInterface $handlerRegistry, ObjectConstructorInterface $objectConstructor, ExclusionStrategyInterface $exclusionStrategy = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->context = new NavigatorContext($direction, $format);
        $this->dispatcher = $dispatcher;
        $this->metadataFactory = $metadataFactory;
        $this->handlerRegistry = $handlerRegistry;
        $this->objectConstructor = $objectConstructor;
        $this->exclusionStrategy = $exclusionStrategy;
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
            if (!$this->context->isSerializing()) {
                $msg = 'The type must be given for all properties when deserializing.';
                if (null !== $path = $this->context->getPath()) {
                    $msg .= ' Path: '.$path;
                }

                throw new RuntimeException($msg);
            }

            $typeName = gettype($data);
            if ('object' === $typeName) {
                $typeName = get_class($data);
            }

            $type = array('name' => $typeName, 'params' => array());
        }
        // If the data is null, we have to force the type to null regardless of the input in order to
        // guarantee correct handling of null values, and not have any internal auto-casting behavior.
        else if ($this->context->isSerializing() && null === $data) {
            $type = array('name' => 'NULL', 'params' => array());
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
            case 'float':
                return $visitor->visitDouble($data, $type);

            case 'array':
                return $visitor->visitArray($data, $type);

            case 'resource':
                $msg = 'Resources are not supported in serialized data.';
                if (null !== $path = $this->context->getPath()) {
                    $msg .= ' Path: '.$path;
                }

                throw new RuntimeException($msg);

            default:
                $isSerializing = $this->context->isSerializing();

                if ($isSerializing && null !== $data) {
                    if ($this->context->isVisiting($data)) {
                        return null;
                    }
                    $this->context->startVisiting($data);
                }

                // Trigger pre-serialization callbacks, and listeners if they exist.
                // Dispatch pre-serialization event before handling data to have ability change type in listener
                if ($isSerializing) {
                    if (null !== $this->dispatcher && $this->dispatcher->hasListeners('serializer.pre_serialize', $type['name'], $this->context->getFormat())) {
                        $this->dispatcher->dispatch('serializer.pre_serialize', $type['name'], $this->context->getFormat(), $event = new PreSerializeEvent($visitor, $data, $type));
                        $type = $event->getType();
                    }
                }

                // First, try whether a custom handler exists for the given type. This is done
                // before loading metadata because the type name might not be a class, but
                // could also simply be an artifical type.
                if (null !== $handler = $this->handlerRegistry->getHandler($this->context->getDirection(), $type['name'], $this->context->getFormat())) {
                    $rs = call_user_func($handler, $visitor, $data, $type, $this->context);

                    $this->context->stopVisiting($data);

                    return $rs;
                }

                // Load metadata, and check whether this class should be excluded.
                $metadata = $this->metadataFactory->getMetadataForClass($type['name']);
                if (null !== $this->exclusionStrategy && $this->exclusionStrategy->shouldSkipClass($metadata, $this->context)) {
                    $this->context->stopVisiting($data);

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

                if (isset($metadata->handlerCallbacks[$this->context->getDirection()][$this->context->getFormat()])) {
                    $rs = $object->{$metadata->handlerCallbacks[$this->context->getDirection()][$this->context->getFormat()]}($visitor, $isSerializing ? null : $data);
                    $this->afterVisitingObject($visitor, $metadata, $object, $type);

                    return $isSerializing ? $rs : $object;
                }

                $visitor->startVisitingObject($metadata, $object, $type);
                foreach ($metadata->propertyMetadata as $propertyMetadata) {
                    if (null !== $this->exclusionStrategy && $this->exclusionStrategy->shouldSkipProperty($propertyMetadata, $this->context)) {
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

        $this->context->stopVisiting($object);
    }

    private function afterVisitingObject(VisitorInterface $visitor, ClassMetadata $metadata, $object, array $type)
    {
        if ($this->context->isSerializing()) {
            $this->context->stopVisiting($object);

            foreach ($metadata->postSerializeMethods as $method) {
                $method->invoke($object);
            }

            if (null !== $this->dispatcher && $this->dispatcher->hasListeners('serializer.post_serialize', $metadata->name, $this->context->getFormat())) {
                $this->dispatcher->dispatch('serializer.post_serialize', $metadata->name, $this->context->getFormat(), new Event($visitor, $object, $type));
            }

            return;
        }

        foreach ($metadata->postDeserializeMethods as $method) {
            $method->invoke($object);
        }

        if (null !== $this->dispatcher && $this->dispatcher->hasListeners('serializer.post_deserialize', $metadata->name, $this->context->getFormat())) {
            $this->dispatcher->dispatch('serializer.post_deserialize', $metadata->name, $this->context->getFormat(), new Event($visitor, $object, $type));
        }
    }
}
