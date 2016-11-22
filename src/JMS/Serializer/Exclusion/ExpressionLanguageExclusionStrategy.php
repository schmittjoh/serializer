<?php


namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionLanguageExclusionStrategy implements ExclusionStrategyInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    private $context = array();

    public function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function addContextVariable($name, $value)
    {
        $this->context[$name] = $value;
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
        if (null === $property->excludeIf) {
            return false;
        }

        $variables = [
            'context' => $navigatorContext,
        ];
        if ($navigatorContext instanceof SerializationContext) {
            $variables['object'] = $navigatorContext->getObject();
        }

        return $this->expressionLanguage->evaluate($property->excludeIf, $variables + $this->context);
    }
}
