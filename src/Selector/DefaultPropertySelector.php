<?php

declare(strict_types=1);

namespace JMS\Serializer\Selector;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class DefaultPropertySelector implements PropertySelectorInterface
{
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $evaluator;
    /**
     * @var ExclusionStrategyInterface
     */
    private $exclusionStrategy;
    /**
     * @var ExpressionLanguageExclusionStrategy
     */
    private $expressionExclusionStrategy;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var array
     */
    private $cache = [];

    public function __construct(
        Context $context,
        ExpressionEvaluatorInterface $evaluator
    ) {
        $this->exclusionStrategy = $context->getExclusionStrategy();
        $this->context = $context;
        $this->evaluator = $evaluator;
        $this->expressionExclusionStrategy = new ExpressionLanguageExclusionStrategy($evaluator);
    }

    /**
     * @param ClassMetadata $metadata
     * @return PropertyMetadata[]
     */
    public function select(ClassMetadata $metadata): array
    {
        if (!isset($this->cache[spl_object_hash($metadata)])) {

            $values = [];
            foreach ($metadata->propertyMetadata as $propertyMetadata) {
                if ($this->context instanceof DeserializationContext && $propertyMetadata->readOnly) {
                    continue;
                }

                if ($this->exclusionStrategy->shouldSkipProperty($propertyMetadata, $this->context) || $this->expressionExclusionStrategy->shouldSkipProperty($propertyMetadata, $this->context)) {
                    continue;
                }

                $values[] = $propertyMetadata;
            }
            $this->cache[spl_object_hash($metadata)] = $values;
        }

        return $this->cache[spl_object_hash($metadata)];
    }
}
