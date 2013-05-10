<?php

require_once 'bootstrap.php';

function benchmark(\Closure $f, $times = 10) {
    $time = microtime(true);
    for ($i=0; $i<$times; $i++) {
        $f();
    }

    return (microtime(true) - $time) / $times;
}

function createCollection() {
    $collection = array();
    for ($i=0; $i<50; $i++) {
        $collection[] = createObject();
    }

    return $collection;
}

function createObject() {
    $post = new \JMS\Serializer\Tests\Fixtures\BlogPost('FooooooooooooooooooooooBAR', new \JMS\Serializer\Tests\Fixtures\Author('Foo'), new \DateTime);
    for ($i=0; $i<10; $i++) {
        $post->addComment(new \JMS\Serializer\Tests\Fixtures\Comment(new \JMS\Serializer\Tests\Fixtures\Author('foo'), 'foobar'));
    }

    return $post;
}

$serializer = \JMS\Serializer\SerializerBuilder::create()->build();
$collection = createCollection();
$metrics = array();

foreach (array('json', 'yml', 'xml') as $format) {
    $metrics['benchmark-collection-'.$format] = benchmark(function() use ($serializer, $collection, $format) {
        $serializer->serialize($collection, $format);
    }, 10);
}

echo json_encode(array('metrics' => $metrics));
