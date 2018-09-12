<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Discriminator(field = "type", map = {
 *    "post": "JMS\Serializer\Tests\Fixtures\Discriminator\Post",
 *    "image_post": "JMS\Serializer\Tests\Fixtures\Discriminator\ImagePost",
 * })
 */
class Post
{
    /** @Serializer\Type("string") */
    public $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }
}
