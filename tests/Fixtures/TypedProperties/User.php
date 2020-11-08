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
     * @Serializer\ReadOnly()
     */
    public ?\DateTimeInterface $updated = null;
    /**
     * @Serializer\ReadOnly()
     */
    public iterable $tags = [];
}
