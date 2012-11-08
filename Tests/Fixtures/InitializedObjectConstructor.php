<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;

use JMS\SerializerBundle\Metadata\ClassMetadata;

use JMS\SerializerBundle\Serializer\Construction\ObjectConstructorInterface;
use JMS\SerializerBundle\Serializer\VisitorInterface;

use JMS\SerializerBundle\Tests\Fixtures\Author;

class InitializedObjectConstructor implements ObjectConstructorInterface
{
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type)
    {
        $post = new \StdClass;
        $post->title = 'This is a nice title.';
        $post->author = new Author('Foo Bar');
        $post->createdAt = new \DateTime('2011-07-30 00:00', new \DateTimeZone('UTC'));
        $post->comments = new ArrayCollection();
        $post->published = false;

        $post->comments->add(new \StdClass);

        return $post;
    }
}
