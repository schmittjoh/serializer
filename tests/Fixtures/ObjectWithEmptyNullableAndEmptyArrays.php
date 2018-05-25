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
    public $null_inline = null;

    /**
     * @Serializer\XmlList(inline = true, entry = "comment")
     * @Serializer\Type("array")
     */
    public $empty_inline = [];

    /**
     * @Serializer\XmlList(inline = true, entry = "comment")
     * @Serializer\Type("array")
     */
    public $not_empty_inline = ['not_empty_inline'];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment")
     * @Serializer\Type("array")
     */
    public $null_not_inline = null;

    /**
     * @Serializer\XmlList(inline = false, entry = "comment")
     * @Serializer\Type("array")
     */
    public $empty_not_inline = [];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $not_empty_not_inline = ['not_empty_not_inline'];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $null_not_inline_skip = null;

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $empty_not_inline_skip = [];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $not_empty_not_inline_skip = ['not_empty_not_inline_skip'];
}
