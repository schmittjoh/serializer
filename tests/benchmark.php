<?php

if ( ! isset($_SERVER['argv'][1], $_SERVER['argv'][2])) {
    echo 'Usage: php benchmark.php <format> <iterations> [output-file]'.PHP_EOL;
    exit(1);
}

list(, $format, $iterations) = $_SERVER['argv'];

require_once 'bootstrap.php';

function benchmark(\Closure $f, $times = 10)
{
    $time = microtime(true);
    for ($i=0; $i<$times; $i++) {
        $f();
    }

    return (microtime(true) - $time) / $times;
}

function createCollection()
{
    $collection = array();
    for ($i=0; $i<50; $i++) {
        $collection[] = createObject();
    }

    return $collection;
}

function createObject()
{
    $post = new \JMS\Serializer\Tests\Fixtures\BlogPost('FooooooooooooooooooooooBAR', new \JMS\Serializer\Tests\Fixtures\Author('Foo'), new \DateTime);
    for ($i=0; $i<10; $i++) {
        $post->addComment(new \JMS\Serializer\Tests\Fixtures\Comment(new \JMS\Serializer\Tests\Fixtures\Author('foo'), 'foobar'));
    }

    return $post;
}

$serializer = \JMS\Serializer\SerializerBuilder::create()->build();
$collection = createCollection();
$metrics = array();
$f = function() use ($serializer, $collection, $format) {
    $serializer->serialize($collection, $format);
};

// Load all necessary classes into memory.
benchmark($f, 1);

printf('Benchmarking collection for format "%s".'.PHP_EOL, $format);
$metrics['benchmark-collection-'.$format] = benchmark($f, $iterations);

$output = json_encode(array('metrics' => $metrics));

if (isset($_SERVER['argv'][3])) {
    file_put_contents($_SERVER['argv'][3], $output);
    echo "Done.".PHP_EOL;
} else {
    echo $output.PHP_EOL;
}
