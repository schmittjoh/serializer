<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Discriminator
{
    /** @var array<string> */
    public $map = [];

    /** @var string */
    public $field = 'type';

    /** @var bool */
    public $disabled = false;

    /** @var string[] */
    public $groups = [];

    public function __construct(array $values = [], string $field = 'type', array $groups = [], array $map = [], bool $disabled = false)
    {
        if (array_key_exists('field', $values)) {
            $field = $values['field'];
        }

        if (array_key_exists('groups', $values)) {
            $groups = $values['groups'];
        }

        if (array_key_exists('map', $values)) {
            $map = $values['map'];
        }

        if (array_key_exists('disabled', $values)) {
            $disabled = $values['disabled'];
        }

        $this->field = $field;
        $this->groups = $groups;
        $this->map = $map;
        $this->disabled = $disabled;
    }
}
