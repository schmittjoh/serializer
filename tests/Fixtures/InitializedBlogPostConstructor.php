<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;

class InitializedBlogPostConstructor extends UnserializeObjectConstructor
{
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        if ($type['name'] !== 'JMS\Serializer\Tests\Fixtures\BlogPost') {
            return parent::construct($visitor, $metadata, $data, $type);
        }

        return new BlogPost('This is a nice title.', new Author('Foo Bar'), new \DateTime('2011-07-30 00:00', new \DateTimeZone('UTC')), new Publisher('Bar Foo'));
    }
}
