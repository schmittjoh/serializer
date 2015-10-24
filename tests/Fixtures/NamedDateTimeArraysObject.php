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
use JMS\Serializer\Annotation\XmlMap;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlKeyValuePairs;


class NamedDateTimeArraysObject
{
    /**
     * @var \DateTime[]
     * @Type("array<string,DateTime<'d.m.Y H:i:s'>>")
     * @XmlKeyValuePairs
     */
    private $namedArrayWithFormattedDate;

    function __construct($namedArrayWithFormattedDate)
    {
        $this->namedArrayWithFormattedDate = $namedArrayWithFormattedDate;
    }

    /**
     * @return \DateTime[]
     */
    public function getNamedArrayWithFormattedDate()
    {
        return $this->namedArrayWithFormattedDate;
    }
}