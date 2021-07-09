<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @XmlRoot("test-object", namespace="http://example.com/namespace")
 */
#[XmlRoot(name: 'test-object', namespace: 'http://example.com/namespace')]
class ObjectWithXmlRootNamespace
{
    /**
     * @Type("string")
     */
    #[Type(name: 'string')]
    private $title;

    /**
     * @Type("DateTime")
     * @XmlAttribute
     */
    #[Type(name: 'DateTime')]
    #[XmlAttribute]
    private $createdAt;

    /**
     * @Type("string")
     * @XmlAttribute
     */
    #[Type(name: 'string')]
    #[XmlAttribute]
    private $etag;

    /**
     * @Type("string")
     */
    #[Type(name: 'string')]
    private $author;

    /**
     * @Type("string")
     * @XmlAttribute
     */
    #[Type(name: 'string')]
    #[XmlAttribute]
    private $language;

    /**
     * @Type("string")
     * @XmlElement(namespace="")
     */
    #[Type(name: 'string')]
    #[XmlElement(namespace: '')]
    private $emptyNsElement;

    public function __construct($title, $author, \DateTime $createdAt, $language, $emptyNsElement)
    {
        $this->title = $title;
        $this->author = $author;
        $this->createdAt = $createdAt;
        $this->language = $language;
        $this->emptyNsElement = $emptyNsElement;
        $this->etag = sha1($this->createdAt->format(\DateTime::ATOM));
    }
}
