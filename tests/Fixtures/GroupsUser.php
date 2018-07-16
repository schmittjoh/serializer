<?php

declare(strict_types=1);

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

    public function __construct($name, ?GroupsUser $manager = null, array $friends = [])
    {
        $this->name = $name;
        $this->manager = $manager;
        $this->friends = $friends;
    }
}
