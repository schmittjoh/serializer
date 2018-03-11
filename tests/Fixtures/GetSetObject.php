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

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\Type;

/** @AccessType("public_method", naming="camel_case") */
class GetSetObject
{
    /** @AccessType("property") @Type("integer") */
    private $id = 1;

    /** @Type("string") */
    private $name = 'Foo';

    /**
     * @ReadOnly
     */
    private $readOnlyProperty = 42;

    /**
     * This property should be exlcluded
     * @Exclude()
     */
    private $excludedProperty;

    /** @Type("string") */
    private $underscored_property;

    /**
     * @Type("string")
     * @AccessType(naming="exact")
     */
    private $force_underscore;

    public function getId()
    {
        throw new \RuntimeException('This should not be called.');
    }

    public function getName()
    {
        return 'Johannes';
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getReadOnlyProperty()
    {
        return $this->readOnlyProperty;
    }

    public function getUnderscoredProperty()
    {
        return $this->underscored_property;
    }

    public function setUnderscoredProperty($underscored_property)
    {
        $this->underscored_property = $underscored_property;
    }

    public function getForce_Underscore()
    {
        return $this->force_underscore;
    }

    public function setForce_Underscore($force_underscore)
    {
        $this->force_underscore = $force_underscore;
    }
}
