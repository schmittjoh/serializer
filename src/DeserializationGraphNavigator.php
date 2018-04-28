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

use JMS\Serializer\Accessor\AccessorStrategyInterface;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\Exception\ExpressionLanguageRequiredException;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use Metadata\MetadataFactoryInterface;

/**
 * Handles traversal along the object graph.
 *
 * This class handles traversal along the graph, and calls different methods
 * on visitors, or custom handlers to process its nodes.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class DeserializationGraphNavigator extends GraphNavigator implements GraphNavigatorInterface
{
    /**
     * @var DeserializationVisitorInterface
     */
    protected $visitor;

    /**
     * @var ExpressionLanguageExclusionStrategy
     */
    private $expressionExclusionStrategy;

    private $dispatcher;
    private $metadataFactory;
    private $handlerRegistry;
    private $objectConstructor;
    /**
     * @var AccessorStrategyInterface
     */
    private $accessor;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        HandlerRegistryInterface $handlerRegistry,
        ObjectConstructorInterface $objectConstructor,
        AccessorStrategyInterface $accessor,
        EventDispatcherInterface $dispatcher = null,
        ExpressionEvaluatorInterface $expressionEvaluator = null
    ) {
        $this->dispatcher = $dispatcher ?: new EventDispatcher();
        $this->metadataFactory = $metadataFactory;
        $this->handlerRegistry = $handlerRegistry;
        $this->objectConstructor = $objectConstructor;
        $this->accessor = $accessor;
        if ($expressionEvaluator) {
            $this->expressionExclusionStrategy = new ExpressionLanguageExclusionStrategy($expressionEvaluator);
        }
    }

    /**
     * Called for each node of the graph that is being traversed.
     *
     * @param mixed $data the data depends on the direction, and type of visitor
     * @param null|array $type array has the format ["name" => string, "params" => array]
     * @return mixed the return value depends on the direction, and type of visitor
     */
    public function accept($data, array $type = null)
    {
        // If the type was not given, we infer the most specific type from the
        // input data in serialization mode.
        if (null === $type) {
            throw new RuntimeException('The type must be given for all properties when deserializing.');
        }
        // Sometimes data can convey null but is not of a null type.
        // Visitors can have the power to add this custom null evaluation
        if ($this->visitor instanceof NullAwareVisitorInterface && $this->visitor->isNull($data) === true) {
            $type = ['name' => 'NULL', 'params' => []];
        }

        switch ($type['name']) {
            case 'NULL':
                return $this->visitor->visitNull($data, $type);

            case 'string':
                return $this->visitor->visitString($data, $type);

            case 'int':
            case 'integer':
                return $this->visitor->visitInteger($data, $type);

            case 'bool':
            case 'boolean':
                return $this->visitor->visitBoolean($data, $type);

            case 'double':
            case 'float':
                return $this->visitor->visitDouble($data, $type);

            case 'array':
                return $this->visitor->visitArray($data, $type);

            case 'resource':
                throw new RuntimeException('Resources are not supported in serialized data.');

            default:

                $this->context->increaseDepth();

                // Trigger pre-serialization callbacks, and listeners if they exist.
                // Dispatch pre-serialization event before handling data to have ability change type in listener
                if ($this->dispatcher->hasListeners('serializer.pre_deserialize', $type['name'], $this->format)) {
                    $this->dispatcher->dispatch('serializer.pre_deserialize', $type['name'], $this->format, $event = new PreDeserializeEvent($this->context, $data, $type));
                    $type = $event->getType();
                    $data = $event->getData();
                }

                // First, try whether a custom handler exists for the given type. This is done
                // before loading metadata because the type name might not be a class, but
                // could also simply be an artifical type.
                if (null !== $handler = $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, $type['name'], $this->format)) {
                    $rs = \call_user_func($handler, $this->visitor, $data, $type, $this->context);
                    $this->context->decreaseDepth();

                    return $rs;
                }

                /** @var $metadata ClassMetadata */
                $metadata = $this->metadataFactory->getMetadataForClass($type['name']);

                if ($metadata->usingExpression && !$this->expressionExclusionStrategy) {
                    throw new ExpressionLanguageRequiredException("To use conditional exclude/expose in {$metadata->name} you must configure the expression language.");
                }

                if (!empty($metadata->discriminatorMap) && $type['name'] === $metadata->discriminatorBaseClass) {
                    $metadata = $this->resolveMetadata($data, $metadata);
                }

                if ($this->exclusionStrategy->shouldSkipClass($metadata, $this->context)) {
                    $this->context->decreaseDepth();

                    return null;
                }

                $this->context->pushClassMetadata($metadata);

                $object = $this->objectConstructor->construct($this->visitor, $metadata, $data, $type, $this->context);

                $this->visitor->startVisitingObject($metadata, $object, $type);
                foreach ($metadata->propertyMetadata as $propertyMetadata) {
                    if ($this->exclusionStrategy->shouldSkipProperty($propertyMetadata, $this->context)) {
                        continue;
                    }

                    if (null !== $this->expressionExclusionStrategy && $this->expressionExclusionStrategy->shouldSkipProperty($propertyMetadata, $this->context)) {
                        continue;
                    }

                    if ($propertyMetadata->readOnly) {
                        continue;
                    }

                    $this->context->pushPropertyMetadata($propertyMetadata);
                    try {
                        $v = $this->visitor->visitProperty($propertyMetadata, $data);
                        $this->accessor->setValue($object, $v, $propertyMetadata);
                    }catch (NotAcceptableException $e){

                    }
                    $this->context->popPropertyMetadata();
                }

                $rs = $this->visitor->endVisitingObject($metadata, $data, $type);
                $this->afterVisitingObject($metadata, $rs, $type);

                return $rs;
        }
    }

    private function resolveMetadata($data, ClassMetadata $metadata)
    {
        $typeValue = $this->visitor->visitDiscriminatorMapProperty($data, $metadata);

        if (!isset($metadata->discriminatorMap[$typeValue])) {
            throw new LogicException(sprintf(
                'The type value "%s" does not exist in the discriminator map of class "%s". Available types: %s',
                $typeValue,
                $metadata->name,
                implode(', ', array_keys($metadata->discriminatorMap))
            ));
        }

        return $this->metadataFactory->getMetadataForClass($metadata->discriminatorMap[$typeValue]);
    }

    private function afterVisitingObject(ClassMetadata $metadata, $object, array $type): void
    {
        $this->context->decreaseDepth();
        $this->context->popClassMetadata();

        foreach ($metadata->postDeserializeMethods as $method) {
            $method->invoke($object);
        }

        if ($this->dispatcher->hasListeners('serializer.post_deserialize', $metadata->name, $this->format)) {
            $this->dispatcher->dispatch('serializer.post_deserialize', $metadata->name, $this->format, new ObjectEvent($this->context, $object, $type));
        }
    }
}
