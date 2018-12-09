<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class BlogPostSeo
{
    /**
     * @ORM\Column(type="string", name="meta_title")
     *
     * @var string
     */
    private $metaTitle;
}
