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

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlKeyValuePairs;

class ObjectWithXmlKeyValuePairsWithType
{
    /**
     * @var array
     * @Type("array<string,string>")
     * @XmlKeyValuePairs
     */
    private $list;

    /**
     * @var array
     * @Type("array<string>")
     */
    private $list2;

    public function __construct(array $list, array $list2 = [])
    {
        $this->list = $list;
        $this->list2 = $list2;
    }

    public static function create1()
    {
        return new self(
            [
                'key-one' => 'foo',
                'key-two' => 'bar',
            ]
        );
    }

    public static function create2()
    {
        return new self(
            [
                'key_01' => 'One',
                'key_02' => 'Two',
                'key_03' => 'Three',
            ],
            [
                'Four',
            ]
        );
    }
}
