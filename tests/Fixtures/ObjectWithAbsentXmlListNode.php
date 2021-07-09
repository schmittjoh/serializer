<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithAbsentXmlListNode
{
    /**
     * @Serializer\XmlList(inline=false, entry="comment", skipWhenEmpty=true)
     * @Serializer\Type("array<string>")
     */
    #[Serializer\XmlList(entry: 'comment', inline: false, skipWhenEmpty: true)]
    #[Serializer\Type(name: 'array<string>')]
    public $absent;
    /**
     * @Serializer\XmlList(inline=false, entry="comment", skipWhenEmpty=false)
     * @Serializer\Type("array<string>")
     */
    #[Serializer\XmlList(entry: 'comment', inline: false, skipWhenEmpty: false)]
    #[Serializer\Type(name: 'array<string>')]
    public $present;

    /**
     * @Serializer\XmlList(inline=false, entry="comment")
     * @Serializer\Type("array<string>")
     */
    #[Serializer\XmlList(entry: 'comment', inline: false)]
    #[Serializer\Type(name: 'array<string>')]
    public $skipDefault;

    /**
     * @Serializer\XmlList(inline=false, namespace="http://www.example.com")
     * @Serializer\Type("array<string>")
     */
    #[Serializer\XmlList(inline: false, namespace: 'http://www.example.com')]
    #[Serializer\Type(name: 'array<string>')]
    public $absentAndNs;
}
