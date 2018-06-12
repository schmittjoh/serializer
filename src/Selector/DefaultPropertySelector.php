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

    public function __construct(
        ExclusionStrategyInterface $exclusionStrategy,
        ExpressionEvaluatorInterface $evaluator
    ) {
        $this->exclusionStrategy = $exclusionStrategy;
        $this->evaluator = $evaluator;
        $this->expressionExclusionStrategy = new ExpressionLanguageExclusionStrategy($evaluator);
    }

    /**
     * @param ClassMetadata $metadata
     * @param Context $context
     * @return PropertyMetadata[]
     */
    public function select(ClassMetadata $metadata, Context $context): array
    {
        $values = [];
        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            if ($context instanceof DeserializationContext && $propertyMetadata->readOnly) {
                continue;
            }

            if ($this->exclusionStrategy->shouldSkipProperty($propertyMetadata, $context) || $this->expressionExclusionStrategy->shouldSkipProperty($propertyMetadata, $context)) {
                continue;
            }

            $values[] = $propertyMetadata;
        }

        return $values;
    }
}
