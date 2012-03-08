<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\SerializedName;
use JMS\SerializerBundle\Annotation\Type;
use JMS\SerializerBundle\Annotation\XmlRoot;
use JMS\SerializerBundle\Annotation\ReadOnly;

/** @XmlRoot("author") */
class AuthorReadOnly
{
    /**
     * @ReadOnly
     * @SerializedName("id")
     */
    private $id;

    /**
     * @Type("string")
     * @SerializedName("full_name")
     */
    private $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}