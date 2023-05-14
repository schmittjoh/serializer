<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

use JMS\Serializer\Annotation as Serializer;

class User
{
    public int $id;
    public Role $role;
    public Vehicle $vehicle;
    public \DateTime $created;

    /**
     * @Serializer\ReadOnlyProperty()
     */
    #[Serializer\ReadOnlyProperty]
    public ?\DateTimeInterface $updated = null;

    /**
     * @Serializer\ReadOnlyProperty()
     */
    #[Serializer\ReadOnlyProperty]
    public iterable $tags = [];

    /**
     * @Serializer\VirtualProperty()
     */
    #[Serializer\VirtualProperty]
    public function getVirtualRole(): Role
    {
        return $this->role;
    }
}
