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

use JMS\Serializer\Annotation as Serializer;

class ObjectWithEmptyNullableAndEmptyArrays
{
    /**
     * @Serializer\XmlList(inline = true, entry = "comment")
     * @Serializer\Type("array")
     */
    public $null_inline = null;

    /**
     * @Serializer\XmlList(inline = true, entry = "comment")
     * @Serializer\Type("array")
     */
    public $empty_inline = [];


    /**
     * @Serializer\XmlList(inline = true, entry = "comment")
     * @Serializer\Type("array")
     */
    public $not_empty_inline = ['not_empty_inline'];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment")
     * @Serializer\Type("array")
     */
    public $null_not_inline = null;

    /**
     * @Serializer\XmlList(inline = false, entry = "comment")
     * @Serializer\Type("array")
     */
    public $empty_not_inline = [];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $not_empty_not_inline = ['not_empty_not_inline'];

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $null_not_inline_skip = null;

    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $empty_not_inline_skip = [];


    /**
     * @Serializer\XmlList(inline = false, entry = "comment", skipWhenEmpty=false)
     * @Serializer\Type("array")
     */
    public $not_empty_not_inline_skip = ['not_empty_not_inline_skip'];
}
