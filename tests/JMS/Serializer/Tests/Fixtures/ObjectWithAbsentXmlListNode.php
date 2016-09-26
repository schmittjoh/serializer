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

class ObjectWithAbsentXmlListNode
{
    /**
     * @Serializer\XmlList(inline=false, entry="comment", skipWhenEmpty=true)
     * @Serializer\Type("array<string>")
     */
    public $absent;
    /**
     * @Serializer\XmlList(inline=false, entry="comment", skipWhenEmpty=false)
     * @Serializer\Type("array<string>")
     */
    public $present;

    /**
     * @Serializer\XmlList(inline=false, entry="comment")
     * @Serializer\Type("array<string>")
     */
    public $skipDefault;
}
