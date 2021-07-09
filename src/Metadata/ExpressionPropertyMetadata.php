<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata;

use JMS\Serializer\Expression\Expression;

/**
 * @Annotation
 * @Target("METHOD")
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class ExpressionPropertyMetadata extends PropertyMetadata
{
    /**
     * @var string|Expression
     */
    public $expression;

    /**
     * @param string|Expression $expression
     */
    public function __construct(string $class, string $fieldName, $expression)
    {
        $this->class = $class;
        $this->name = $fieldName;
        $this->expression = $expression;
        $this->readOnly = true;
    }

    public function setAccessor(string $type, ?string $getter = null, ?string $setter = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->expression,
            parent::serialize(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        $parentStr = $this->unserializeProperties($str);
        [$this->class, $this->name] = unserialize($parentStr);
    }

    protected function unserializeProperties(string $str): string
    {
        [
            $this->expression,
            $parentStr,
        ] = unserialize($str);

        return parent::unserializeProperties($parentStr);
    }
}
