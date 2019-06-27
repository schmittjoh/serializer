<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

class InitializedBlogPostConstructor implements ObjectConstructorInterface
{
    private $fallback;

    /**
     * @var BlogPost
     */
    private $blogPost;

    public function __construct(BlogPost $blogPost)
    {
        $this->fallback = new UnserializeObjectConstructor();
        $this->blogPost = $blogPost;
    }

    public function construct(DeserializationVisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context): ?object
    {
        if ('JMS\Serializer\Tests\Fixtures\BlogPost' !== $type['name']) {
            return $this->fallback->construct($visitor, $metadata, $data, $type, $context);
        }

        return $this->blogPost;
    }
}
