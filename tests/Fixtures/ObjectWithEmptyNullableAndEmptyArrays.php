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
    #[Serializer\XmlList(entry: 'comment', inline: true)]
    #[Serializer\Type(name: 'array')]
    public $nullInline = null;

    /**
     * @Serializer\XmlList(inline = true, entry = "comment")
     * @Serializer\Type("array")
     */
    #[Serializer\XmlList(entry: 'comment', inline: true)]
    #[Serializer\Type(name: 'array')]
    public $emptyInline = [];

    /**
     * @Serializer\XmlList(inline = true, entry = "comment")
     * @Serializer\Type("array")
     */
    #[Serializer\XmlList(entry: 'comment', inline: true)]
    #[Serializer\Type(name: 'array')]
    public $notEmptyInline = ['not_empty_inline'];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment")
     * @Serializer\Type("array")
     */
    #[Serializer\XmlList(entry: 'comment', inline: false)]
    #[Serializer\Type(name: 'array')]
    public $nullNotInline = null;

    /**
     * @Serializer\XmlList(inline = false, entry = "comment")
     * @Serializer\Type("array")
     */
    #[Serializer\XmlList(entry: 'comment', inline: false)]
    #[Serializer\Type(name: 'array')]
    public $emptyNotInline = [];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    #[Serializer\XmlList(entry: 'comment', inline: false, skipWhenEmpty: false)]
    #[Serializer\Type(name: 'array')]
    public $notEmptyNotInline = ['not_empty_not_inline'];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    #[Serializer\XmlList(entry: 'comment', inline: false, skipWhenEmpty: false)]
    #[Serializer\Type(name: 'array')]
    public $nullNotInlineSkip = null;

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    #[Serializer\XmlList(entry: 'comment', inline: false, skipWhenEmpty: false)]
    #[Serializer\Type(name: 'array')]
    public $emptyNotInlineSkip = [];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    #[Serializer\XmlList(entry: 'comment', inline: false, skipWhenEmpty: false)]
    #[Serializer\Type(name: 'array')]
    public $notEmptyNotInlineSkip = ['not_empty_not_inline_skip'];
}
