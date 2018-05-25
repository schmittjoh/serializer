<?php

namespace JMS\Serializer\Accessor;

use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class ExpressionAccessorStrategy implements AccessorStrategyInterface
{
    /**
     * @var AccessorStrategyInterface
     */
    private $fallback;
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $evaluator;

    public function __construct(ExpressionEvaluatorInterface $evaluator, AccessorStrategyInterface $fallback)
    {
        $this->fallback = $fallback;
        $this->evaluator = $evaluator;
    }

    public function getValue($object, PropertyMetadata $metadata)
    {
        if ($metadata instanceof ExpressionPropertyMetadata) {
            return $this->evaluator->evaluate($metadata->expression, array('object' => $object));
        }
        return $this->fallback->getValue($object, $metadata);
    }

    public function setValue($object, $value, PropertyMetadata $metadata)
    {
        $this->fallback->setValue($object, $value, $metadata);
    }
}
