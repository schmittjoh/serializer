<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlMap;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @XmlRoot("ObjectWithNamespacesAndNestedList", namespace="http://example.com/namespace")
 * @XmlNamespace(uri="http://example.com/namespace")
 * @XmlNamespace(uri="http://example.com/namespace2", prefix="x")
 */
class ObjectWithNamespacesAndNestedList
{
    /**
     * @Type("JMS\Serializer\Tests\Fixtures\PersonCollection")
     * @SerializedName("person_collection")
     */
    public $personCollection;
}

