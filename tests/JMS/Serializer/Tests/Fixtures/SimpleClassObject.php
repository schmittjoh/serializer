<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
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

use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlElement;

/**
 * @XmlNamespace(prefix="old_foo", uri="http://old.foo.example.org");
 * @XmlNamespace(prefix="foo", uri="http://foo.example.org");
 * @XmlNamespace(prefix="new_foo", uri="http://new.foo.example.org");
 */
class SimpleClassObject
{
    /**
     * @Type("string")
     * @XmlAttribute(namespace="http://old.foo.example.org")
     */
    public $foo;
    
    /**
     * @Type("string")
     * @XmlElement(namespace="http://foo.example.org")
     */
    public $bar;

    /**
     * @Type("string")
     * @XmlElement(namespace="http://new.foo.example.org")
     */
    public $moo;

}
