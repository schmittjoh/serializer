<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\Since;

/** blablub */
class ChainObject
{
    /**
     * @Until("1")
     * @Type("string")
     */
    private $foo;

    /**
     * @Groups({"Default"})
     * @Since("2")
     * @Type("string")
     */
    private $bar;

    /**
     * @Groups({"bar"})
     * @Since("2")
     * @Type("string")
     */
    private $baz;

    public function __construct()
    {
        $this->foo  = "foo";
        $this->bar = "bar";
        $this->baz = "baz";
    }
}
