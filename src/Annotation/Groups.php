<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD","ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class Groups
{
    /** @var array<string> @Required */
    public $groups = [];

    public function __construct(array $values = [], array $groups = [])
    {
        if (array_key_exists('value', $values)) {
            $groups = $values['value'];
        }

        if (array_key_exists('groups', $values)) {
            $groups = $values['groups'];
        }

        $this->groups = $groups;
    }
}
