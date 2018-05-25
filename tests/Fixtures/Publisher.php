<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @XmlRoot("publisher")
 * @XmlNamespace(uri="http://example.com/namespace2", prefix="ns2")
 */
class Publisher
{
    /**
     * @Type("string")
     * @XmlElement(namespace="http://example.com/namespace2")
     * @SerializedName("pub_name")
     */
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
