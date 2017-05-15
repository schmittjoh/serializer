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


class DateTimeArraysObject
{
    /**
     * @var \DateTime[]
     * @Type("array<DateTime>")
     */
    private $arrayWithDefaultDateTime;

    /**
     * @var \DateTime[]
     * @Type("array<DateTime<'d.m.Y H:i:s'>>")
     */
    private $arrayWithFormattedDateTime;


    function __construct($arrayWithDefaultDateTime, $arrayWithFormattedDateTime)
    {
        $this->arrayWithDefaultDateTime = $arrayWithDefaultDateTime;
        $this->arrayWithFormattedDateTime = $arrayWithFormattedDateTime;
    }

    /**
     * @return \DateTime[]
     */
    public function getArrayWithDefaultDateTime()
    {
        return $this->arrayWithDefaultDateTime;
    }

    /**
     * @return \DateTime[]
     */
    public function getArrayWithFormattedDateTime()
    {
        return $this->arrayWithFormattedDateTime;
    }

}
