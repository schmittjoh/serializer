<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "CLASS"})
 */
final class ExcludeForGroups
{
    /** @var array<string> @Required */
    public $groups;
}