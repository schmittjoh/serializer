<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

abstract class XmlCollection
{
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
        if (array_key_exists('entry', $values)) {
            $entry = $values['entry'];
        }

        if (array_key_exists('inline', $values)) {
            $inline = $values['inline'];
        }

        if (array_key_exists('namespace', $values)) {
            $namespace = $values['namespace'];
        }

        if (array_key_exists('skipWhenEmpty', $values)) {
            $skipWhenEmpty = $values['skipWhenEmpty'];
        }

        $this->entry = $entry;
        $this->inline = $inline;
        $this->namespace = $namespace;
        $this->skipWhenEmpty = $skipWhenEmpty;
    }
}
