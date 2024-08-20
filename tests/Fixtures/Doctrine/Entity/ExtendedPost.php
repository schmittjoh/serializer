<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\Entity;

use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 */
#[Entity]
class ExtendedPost extends BlogPost
{
}
