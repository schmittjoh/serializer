<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\BlogPost;
use JMS\Serializer\Tests\Fixtures\Comment;
use JMS\Serializer\Tests\Fixtures\Publisher;

abstract class AbstractSerializationBench
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var array|BlogPost[]
     */
    private $collection;

    /**
     * @var string
     */
    private $format;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()->build();
        $this->collection = $this->createCollection();
        $this->format = $this->getFormat();
    }

    public function benchSerialization(): void
    {
        $this->serializer->serialize($this->collection, $this->format, $this->createContext());
    }

    abstract protected function getFormat(): string;

    protected function createContext(): SerializationContext
    {
        return new SerializationContext();
    }

    private function createCollection()
    {
        $collection = [];
        for ($i = 0; $i < 200; $i++) {
            $collection[] = $this->createPost();
        }

        return $collection;
    }

    private function createPost()
    {
        $post = new BlogPost(
            'FooooooooooooooooooooooBAR',
            new Author('Foo'),
            new \DateTime(),
            new Publisher('bar')
        );
        for ($i = 0; $i < 100; $i++) {
            $post->addComment(new Comment(new Author('foo'), 'foobar'));
        }

        return $post;
    }
}
