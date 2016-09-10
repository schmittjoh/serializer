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

use JMS\Serializer\Annotation\Groups;

class GroupsUser
{
    private $name;

    /**
     * @Groups({"nickname_group"})
     */
    private $nickname = 'nickname';

    /**
     * @Groups({"manager_group"})
     */
    private $manager;

    /**
     * @Groups({"friends_group"})
     */
    private $friends;

    public function __construct($name, GroupsUser $manager = null, array $friends = array())
    {
        $this->name = $name;
        $this->manager = $manager;
        $this->friends = $friends;
    }
}
