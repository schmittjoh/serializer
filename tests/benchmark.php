<?php

declare(strict_types=1);

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\BlogPost;
use JMS\Serializer\Tests\Fixtures\Comment;
use JMS\Serializer\Tests\Fixtures\Publisher;

if (!isset($_SERVER['argv'][1], $_SERVER['argv'][2])) {
    echo 'Usage: php benchmark.php <format> <iterations> [output-file]' . PHP_EOL;
    exit(1);
}

[, $format, $iterations] = $_SERVER['argv'];

require_once 'bootstrap.php';

function benchmark(Closure $f, $times = 10)
{
    $time = microtime(true);
    for ($i = 0; $i < $times; $i++) {
        $f();
    }

    return (microtime(true) - $time) / $times;
}

function createCollection()
{
    $collection = [];
    for ($i = 0; $i < 200; $i++) {
        $collection[] = createObject();
    }

    return $collection;
}

function createObject()
{
    $p = new Publisher('bar');
    $post = new BlogPost('FooooooooooooooooooooooBAR', new Author('Foo'), new DateTime(), $p);
    for ($i = 0; $i < 100; $i++) {
        $post->addComment(new Comment(new Author('foo'), 'foobar'));
    }

    return $post;
}

$serializer = SerializerBuilder::create()->build();
$collection = createCollection();
$metrics = [];
$f = static function () use ($serializer, $collection, $format) {
    $serializer->serialize($collection, $format);
};

// Load all necessary classes into memory.
benchmark($f, 1);

printf('Benchmarking collection for format "%s".' . PHP_EOL, $format);
$metrics['benchmark-collection-' . $format] = benchmark($f, $iterations);

$output = json_encode(['metrics' => $metrics]);

if (isset($_SERVER['argv'][3])) {
    file_put_contents($_SERVER['argv'][3], $output);
    echo 'Done.' . PHP_EOL;
} else {
    echo $output . PHP_EOL;
}
