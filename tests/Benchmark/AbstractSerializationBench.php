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

    /**
     * @var int
     */
    protected $iterations = 1;

    /**
     * @var int
     */
    protected $amountOfPosts = 200;

    /**
     * @var int
     */
    protected $amountOfComments = 100;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()->build();
        $this->collection = $this->createCollection();
        $this->format = $this->getFormat();
    }

    public function benchSerialization(): void
    {
        for ($i = 0; $i <= $this->iterations; $i++) {
            $this->serializer->serialize($this->collection, $this->format, $this->createContext());
        }
    }

    abstract protected function getFormat(): string;

    protected function createContext(): SerializationContext
    {
        return new SerializationContext();
    }

    private function createCollection()
    {
        $collection = [];
        for ($i = 0; $i < $this->amountOfPosts; $i++) {
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
        for ($i = 0; $i < $this->amountOfComments; $i++) {
            $post->addComment(new Comment(new Author('foo'), 'foobar'));
        }

        return $post;
    }
}
