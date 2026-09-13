<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
#[ORM\Entity]
class UserWithPropertyDefault implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    #[ORM\Id]
    #[ORM\Column('id', Types::INTEGER)]
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    #[ORM\Column('username', Types::STRING)]
    private string $username;

    /**
     * @ORM\Column(type="boolean")
     */
    #[ORM\Column('admin', Types::BOOLEAN)]
    private bool $admin = false;

    public function __construct(int $id, string $username)
    {
        $this->id = $id;
        $this->username = $username;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }
}
