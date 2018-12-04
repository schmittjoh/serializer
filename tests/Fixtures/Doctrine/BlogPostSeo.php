<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine;

use JMS\Serializer\Annotation\SerializedName;

/**
 * @ORM\Embeddable()
 *
 */
class BlogPostSeo
{
    /**
     * @ORM\Column(type="string", name="meta_title")
     * @SerializedName("meta_title")
     * @var string
     */
    private $metaTitle;

    /**
     * @ORM\Column(type="string", name="meta_description")
     * @SerializedName("meta_description")
     * @var string
     */
    private $metaDescription;

    public function getMetaTitle(): string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(string $metaTitle): BlogPostSeo
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(string $metaDescription): BlogPostSeo
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }
}