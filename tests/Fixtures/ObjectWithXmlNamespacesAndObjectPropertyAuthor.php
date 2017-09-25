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

/**
 * @XmlNamespace(uri="http://example.com/namespace-author")
 */
class ObjectWithXmlNamespacesAndObjectPropertyAuthor
{
    /**
     * @Type("string")
     * @XmlElement(namespace="http://example.com/namespace-modified");
     */
    private $author;

    /**
     * @Type("string")
     * @XmlElement(namespace="http://example.com/namespace-author");
     */
    private $info = "hidden-info";

    /**
     * @Type("string")
     * @XmlElement(namespace="http://example.com/namespace-property")
     */
    private $name;

    public function __construct($name, $author)
    {
        $this->name = $name;
        $this->author = $author;
    }
}
