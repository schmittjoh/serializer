<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

abstract class Version implements SerializerAttribute
{
    use AnnotationUtilsTrait;

    /**
     * @Required
     * @var string|null
     */
    public $version = null;

    public function __construct($values = [], ?string $version = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
