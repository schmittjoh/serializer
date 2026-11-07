<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\Entity;

interface UserInterface
{
    public function getId(): int;

    public function getUsername(): string;

    public function isAdmin(): bool;
}
