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

use JMS\Serializer\Annotation as Serializer;

class ObjectList
{
    /**
     * @var SimpleObject[]
     *
     * @Serializer\Type("array<JMS\Serializer\Tests\Fixtures\SimpleObject>")
     * @Serializer\SerializedName("children")
     * @Serializer\XmlList(inline = false, entry = "child")
     */
    public $children = [];

    /**
     * @var ObjectList
     *
     * @Serializer\Type("JMS\Serializer\Tests\Fixtures\ObjectList")
     * @Serializer\SerializedName("list")
     */
    public $objectList;

    /**
     * ObjectList constructor.
     *
     * @param array $children
     * @param null  $objectList
     */
    public function __construct($children = [], $objectList = null)
    {
        $this->children = $children;
        $this->objectList = $objectList;
    }
}
