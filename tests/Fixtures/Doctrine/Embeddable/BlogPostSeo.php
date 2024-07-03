<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\Embeddable;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
#[ORM\Embeddable]
class BlogPostSeo
{
    /**
     * @ORM\Column(type="string", name="meta_title")
     *
     * @var string
     */
    #[ORM\Column(type: Types::STRING, name: 'meta_title')]
    private $metaTitle;
}
