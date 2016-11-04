<?php


namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;

class DynamicExclusionStrategy implements ExclusionStrategyInterface
{
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $expressionEvaluator;

    public function __construct(ExpressionEvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $navigatorContext)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext)
    {
        if (null === $property->excludeIfExpression) {
            return false;
        }

        $variables = [
            'context' => $navigatorContext,
        ];
        if ($navigatorContext instanceof SerializationContext) {
            $variables['object'] = $navigatorContext->getObject();
        }

        return $this->expressionEvaluator->evaluate($property->excludeIfExpression, $variables);
    }
}
