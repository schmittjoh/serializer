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

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlList;

/**
 * @XmlRoot("ObjectWithNamespacesAndListInList", namespace="http://example.com/namespace")
 * @XmlNamespace(uri="http://example.com/namespace")
 */
class ObjectWithNamespacesAndListInList
{
    /**
     * @Type("array<JMS\Serializer\Tests\Fixtures\ObjectList>")
     * @SerializedName("lists")
     * @XmlList(inline = false, entry = "list")
     */
    public $lists = [];
}

