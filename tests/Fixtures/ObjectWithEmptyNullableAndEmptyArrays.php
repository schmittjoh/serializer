<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithEmptyNullableAndEmptyArrays
{
    /**
     * @Serializer\XmlList(inline = true, entry = "comment")
     * @Serializer\Type("array")
     */
    public $nullInline = null;

    /**
     * @Serializer\XmlList(inline = true, entry = "comment")
     * @Serializer\Type("array")
     */
    public $emptyInline = [];

    /**
     * @Serializer\XmlList(inline = true, entry = "comment")
     * @Serializer\Type("array")
     */
    public $notEmptyInline = ['not_empty_inline'];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment")
     * @Serializer\Type("array")
     */
    public $nullNotInline = null;

    /**
     * @Serializer\XmlList(inline = false, entry = "comment")
     * @Serializer\Type("array")
     */
    public $emptyNotInline = [];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $notEmptyNotInline = ['not_empty_not_inline'];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $nullNotInlineSkip = null;

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $emptyNotInlineSkip = [];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $notEmptyNotInlineSkip = ['not_empty_not_inline_skip'];
}
