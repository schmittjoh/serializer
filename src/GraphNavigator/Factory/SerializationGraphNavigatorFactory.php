<?php

declare(strict_types=1);

namespace JMS\Serializer\GraphNavigator\Factory;

use JMS\Serializer\Accessor\AccessorStrategyInterface;
use JMS\Serializer\Accessor\DefaultAccessorStrategy;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\GraphNavigator\SerializationGraphNavigator;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use Metadata\MetadataFactoryInterface;

final class SerializationGraphNavigatorFactory implements GraphNavigatorFactoryInterface
{
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
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $expressionEvaluator;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        HandlerRegistryInterface $handlerRegistry,
        ?AccessorStrategyInterface $accessor = null,
        ?EventDispatcherInterface $dispatcher = null,
        ?ExpressionEvaluatorInterface $expressionEvaluator = null
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->handlerRegistry = $handlerRegistry;
        $this->accessor = $accessor ?: new DefaultAccessorStrategy();
        $this->dispatcher = $dispatcher ?: new EventDispatcher();
        $this->expressionEvaluator = $expressionEvaluator;
    }

    public function getGraphNavigator(): GraphNavigatorInterface
    {
        return new SerializationGraphNavigator($this->metadataFactory, $this->handlerRegistry, $this->accessor, $this->dispatcher, $this->expressionEvaluator);
    }
}
