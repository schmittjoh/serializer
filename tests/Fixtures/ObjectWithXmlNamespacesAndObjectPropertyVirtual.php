<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @XmlRoot("property:test-object", namespace="http://example.com/namespace-property")
 * @XmlNamespace(uri="http://example.com/namespace-property", prefix="property")
 */
class ObjectWithXmlNamespacesAndObjectPropertyVirtual
{
    /**
     * @Type("string")
     * @XmlElement(namespace="http://example.com/namespace-property");
     */
    private $title;

    /**
     * @Type("ObjectWithXmlNamespacesAndObjectPropertyAuthorVirtual")
     * @XmlElement(namespace="http://example.com/namespace-property")
     */
    private $author;

    public function __construct($title, $author)
    {
        $this->title = $title;
        $this->author = $author;
    }
}
