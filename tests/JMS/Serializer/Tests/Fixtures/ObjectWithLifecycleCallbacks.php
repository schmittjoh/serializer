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

use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;
use JMS\Serializer\Annotation\PostSerialize;
use JMS\Serializer\Annotation\PostDeserialize;
use JMS\Serializer\Annotation\Type;

class ObjectWithLifecycleCallbacks
{
    /**
     * @Exclude
     */
    private $firstname;

    /**
     * @Exclude
     */
    private $lastname;

    /**
     * @Type("string")
     */
    private $name;

    public function __construct($firstname = 'Foo', $lastname = 'Bar')
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    /**
     * @PreSerialize
     */
    private function prepareForSerialization()
    {
        $this->name = $this->firstname.' '.$this->lastname;
    }

    /**
     * @PostSerialize
     */
    private function cleanUpAfterSerialization()
    {
        $this->name = null;
    }

    /**
     * @PostDeserialize
     */
    private function afterDeserialization()
    {
        list($this->firstname, $this->lastname) = explode(' ', $this->name);
        $this->name = null;
    }
}
