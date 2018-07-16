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

    public function __construct()
    {
        $this->fallback = new UnserializeObjectConstructor();
    }

    public function construct(DeserializationVisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context): ?object
    {
        if ('JMS\Serializer\Tests\Fixtures\BlogPost' !== $type['name']) {
            return $this->fallback->construct($visitor, $metadata, $data, $type, $context);
        }

        return new BlogPost('This is a nice title.', new Author('Foo Bar'), new \DateTime('2011-07-30 00:00', new \DateTimeZone('UTC')), new Publisher('Bar Foo'));
    }
}
