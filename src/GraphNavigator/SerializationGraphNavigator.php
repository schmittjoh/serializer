<?php

declare(strict_types=1);

namespace JMS\Serializer\GraphNavigator;

use JMS\Serializer\Accessor\AccessorStrategyInterface;
use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\Exception\CircularReferenceDetectedException;
use JMS\Serializer\Exception\ExcludedClassException;
use JMS\Serializer\Exception\ExpressionLanguageRequiredException;
use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exception\SkipHandlerException;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Functions;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\NullAwareVisitorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\VisitorInterface;
use Metadata\MetadataFactoryInterface;

use function assert;

/**
 * Handles traversal along the object graph.
 *
 * This class handles traversal along the graph, and calls different methods
 * on visitors, or custom handlers to process its nodes.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class SerializationGraphNavigator extends GraphNavigator
{
    /**
     * @var SerializationVisitorInterface
     */
    protected $visitor;

    /**
     * @var SerializationContext
     */
    protected $context;

    /**
     * @var ExpressionLanguageExclusionStrategy
     */
    private $expressionExclusionStrategy;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var HandlerRegistryInterface
     */
    private $handlerRegistry;
    /**
     * @var AccessorStrategyInterface
     */
    private $accessor;

    /**
     * @var bool
     */
    private $shouldSerializeNull;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        HandlerRegistryInterface $handlerRegistry,
        AccessorStrategyInterface $accessor,
        ?EventDispatcherInterface $dispatcher = null,
        ?ExpressionEvaluatorInterface $expressionEvaluator = null
    ) {
        $this->dispatcher = $dispatcher ?: new EventDispatcher();
        $this->metadataFactory = $metadataFactory;
        $this->handlerRegistry = $handlerRegistry;
        $this->accessor = $accessor;

        if ($expressionEvaluator) {
            $this->expressionExclusionStrategy = new ExpressionLanguageExclusionStrategy($expressionEvaluator);
        }
    }

    public function initialize(VisitorInterface $visitor, Context $context): void
    {
        assert($context instanceof SerializationContext);

        parent::initialize($visitor, $context);
        $this->shouldSerializeNull = $context->shouldSerializeNull();
    }

    /**
     * Called for each node of the graph that is being traversed.
     *
     * @param mixed $data the data depends on the direction, and type of visitor
     * @param array|null $type array has the format ["name" => string, "params" => array]
     *
     * @return mixed the return value depends on the direction, and type of visitor
     */
    public function accept($data, ?array $type = null)
    {
        // If the type was not given, we infer the most specific type from the
        // input data in serialization mode.
        if (null === $type) {
            $typeName = \gettype($data);
            if ('object' === $typeName) {
                $typeName = \get_class($data);
            }

            $type = ['name' => $typeName, 'params' => []];
        } elseif (null === $data) {
            // If the data is null, we have to force the type to null regardless of the input in order to
            // guarantee correct handling of null values, and not have any internal auto-casting behavior.
            $type = ['name' => 'NULL', 'params' => []];
        }

        // Sometimes data can convey null but is not of a null type.
        // Visitors can have the power to add this custom null evaluation
        if ($this->visitor instanceof NullAwareVisitorInterface && true === $this->visitor->isNull($data)) {
            $type = ['name' => 'NULL', 'params' => []];
        }

        switch ($type['name']) {
            case 'NULL':
                if (!$this->shouldSerializeNull && !$this->isRootNullAllowed()) {
                    throw new NotAcceptableException();
                }

                return $this->visitor->visitNull($data, $type);

            case 'string':
                return $this->visitor->visitString((string) $data, $type);

            case 'int':
            case 'integer':
                return $this->visitor->visitInteger((int) $data, $type);

            case 'bool':
            case 'boolean':
                return $this->visitor->visitBoolean((bool) $data, $type);

            case 'double':
            case 'float':
                return $this->visitor->visitDouble((float) $data, $type);

            case 'iterable':
                return $this->visitor->visitArray(Functions::iterableToArray($data), $type);

            case 'array':
                return $this->visitor->visitArray((array) $data, $type);

            case 'resource':
                $msg = 'Resources are not supported in serialized data.';
                if (null !== $path = $this->context->getPath()) {
                    $msg .= ' Path: ' . $path;
                }

                throw new RuntimeException($msg);

            default:
                if (null !== $data) {
                    if ($this->context->isVisiting($data)) {
                        throw new CircularReferenceDetectedException();
                    }

                    $this->context->startVisiting($data);
                }

                // If we're serializing a polymorphic type, then we'll be interested in the
                // metadata for the actual type of the object, not the base class.
                if (class_exists($type['name'], false) || interface_exists($type['name'], false)) {
                    if (is_subclass_of($data, $type['name'], false)) {
                        $type = ['name' => \get_class($data), 'params' => $type['params'] ?? []];
                    }
                }

                // Trigger pre-serialization callbacks, and listeners if they exist.
                // Dispatch pre-serialization event before handling data to have ability change type in listener
                if ($this->dispatcher->hasListeners('serializer.pre_serialize', $type['name'], $this->format)) {
                    $this->dispatcher->dispatch('serializer.pre_serialize', $type['name'], $this->format, $event = new PreSerializeEvent($this->context, $data, $type));
                    $type = $event->getType();
                }

                // First, try whether a custom handler exists for the given type. This is done
                // before loading metadata because the type name might not be a class, but
                // could also simply be an artifical type.
                if (null !== $handler = $this->handlerRegistry->getHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, $type['name'], $this->format)) {
                    try {
                        $rs = \call_user_func($handler, $this->visitor, $data, $type, $this->context);
                        $this->context->stopVisiting($data);

                        return $rs;
                    } catch (SkipHandlerException $e) {
                        // Skip handler, fallback to default behavior
                    } catch (NotAcceptableException $e) {
                        $this->context->stopVisiting($data);

                        throw $e;
                    }
                }

                $metadata = $this->metadataFactory->getMetadataForClass($type['name']);
                \assert($metadata instanceof ClassMetadata);

                if ($metadata->usingExpression && null === $this->expressionExclusionStrategy) {
                    throw new ExpressionLanguageRequiredException(sprintf('To use conditional exclude/expose in %s you must configure the expression language.', $metadata->name));
                }

                if (null !== $this->exclusionStrategy && $this->exclusionStrategy->shouldSkipClass($metadata, $this->context)) {
                    $this->context->stopVisiting($data);

                    throw new ExcludedClassException();
                }

                if (null !== $this->expressionExclusionStrategy && $this->expressionExclusionStrategy->shouldSkipClass($metadata, $this->context)) {
                    $this->context->stopVisiting($data);

                    throw new ExcludedClassException();
                }

                $this->context->pushClassMetadata($metadata);

                foreach ($metadata->preSerializeMethods as $method) {
                    $method->invoke($data);
                }

                $this->visitor->startVisitingObject($metadata, $data, $type);
                foreach ($metadata->propertyMetadata as $propertyMetadata) {
                    if (null !== $this->exclusionStrategy && $this->exclusionStrategy->shouldSkipProperty($propertyMetadata, $this->context)) {
                        continue;
                    }

                    if (null !== $this->expressionExclusionStrategy && $this->expressionExclusionStrategy->shouldSkipProperty($propertyMetadata, $this->context)) {
                        continue;
                    }

                    $v = $this->accessor->getValue($data, $propertyMetadata, $this->context);

                    if (null === $v && true !== $this->shouldSerializeNull) {
                        continue;
                    }

                    $this->context->pushPropertyMetadata($propertyMetadata);
                    $this->visitor->visitProperty($propertyMetadata, $v);
                    $this->context->popPropertyMetadata();
                }

                $this->afterVisitingObject($metadata, $data, $type);

                return $this->visitor->endVisitingObject($metadata, $data, $type);
        }
    }

    private function isRootNullAllowed(): bool
    {
        return $this->context->hasAttribute('allows_root_null') && $this->context->getAttribute('allows_root_null') && 0 === $this->context->getVisitingSet()->count();
    }

    private function afterVisitingObject(ClassMetadata $metadata, object $object, array $type): void
    {
        $this->context->stopVisiting($object);
        $this->context->popClassMetadata();

        foreach ($metadata->postSerializeMethods as $method) {
            $method->invoke($object);
        }

        if ($this->dispatcher->hasListeners('serializer.post_serialize', $metadata->name, $this->format)) {
            $this->dispatcher->dispatch('serializer.post_serialize', $metadata->name, $this->format, new ObjectEvent($this->context, $object, $type));
        }
    }
}
