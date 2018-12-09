<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class BlogPostWithEmbedded
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Embedded(class="BlogPostSeo", columnPrefix="seo_")
     */
    private $seo;
}
