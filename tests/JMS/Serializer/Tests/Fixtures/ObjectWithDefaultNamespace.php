<?php
/**
 * @author Mike Lively <mike.lively@sellingsource.com>
 */

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlAttribute;

/**
 * @XmlRoot("test-object", namespace="http://example.com/namespace")
 * @XmlNamespace(uri="http://example.com/namespace")
 * @XmlNamespace(uri="http://example.com/namespace/v2", prefix="v2")
 */
class ObjectWithDefaultNamespace
{
    /**
     * @Type("string")
     * @XmlElement(namespace="http://example.com/namespace");
     */
    private $name;

    /**
     * @Type("string")
     * @XmlAttribute(namespace="http://example.com/namespace");
     */
    private $color;

    /**
     * @Type("string")
     * @XmlElement(namespace="http://example.com/namespace");
     */
    private $description;

    /**
     * @Type("string")
     * @XmlElement(namespace="http://example.com/namespace/v2");
     */
    private $status;

    public function __construct($name, $color, $description, $status)
    {
        $this->name = $name;
        $this->color = $color;
        $this->description = $description;
        $this->status = $status;
    }

} 