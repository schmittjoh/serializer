<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlNamespace;

/**
 * @XmlNamespace(prefix="old_foo", uri="http://foo.example.org");
 * @XmlNamespace(prefix="foo", uri="http://better.foo.example.org");
 */
class SimpleSubClassObject
    extends SimpleClassObject
{

    /**
     * @Type("string")
     * @XmlElement(namespace="http://better.foo.example.org")
     */
    public $moo;

    /**
     * @Type("string")
     * @XmlElement(namespace="http://foo.example.org")
     */
    public $baz;

    /**
     * @Type("string")
     * @XmlElement(namespace="http://new.foo.example.org")
     */
    public $qux;

}
