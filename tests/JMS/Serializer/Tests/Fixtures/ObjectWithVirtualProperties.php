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

use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Exclude;

/**
 * @AccessorOrder("custom", custom = {"realField", "virtualField", "renamedVirtualField", "typedVirtualProperty", "foo" })
 */
class ObjectWithVirtualProperties
{

    /**
     * @Type("string")
     */
    protected $realField = 'real field value';

    /**
     *
     * @VirtualProperty
     */
    public function getVirtualField()
    {
        return 'virtual field value';
    }

    /**
     * @VirtualProperty
     * @SerializedName("renamed_virtual_field")
     */
    public function getVirtualFieldToBeRenamed()
    {
        return 'renamed field value';
    }

    /**
     * @VirtualProperty
     * @Type("integer")
     */
    public function getTypedVirtualField()
    {
        return '1';
    }
}
