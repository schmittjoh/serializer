<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

abstract class XmlCollection implements SerializerAttribute
{
    use AnnotationUtilsTrait;

    /**
     * @var string
     */
    public $entry = 'entry';

    /**
     * @var bool
     */
    public $inline = false;

    /**
     * @var string|null
     */
    public $namespace = null;

    /**
     * @var bool
     */
    public $skipWhenEmpty = true;

    public function __construct(array $values = [], string $entry = 'entry', bool $inline = false, ?string $namespace = null, bool $skipWhenEmpty = true)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
