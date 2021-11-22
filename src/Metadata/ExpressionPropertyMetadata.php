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
    protected function serializeToArray(): array
    {
        return [
            $this->expression,
            parent::serializeToArray(),
        ];
    }

    protected function unserializeFromArray(array $data): void
    {
        [
            $this->expression,
            $parentData,
        ] = $data;

        parent::unserializeFromArray($parentData);
    }
}
