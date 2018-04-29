<?php

declare(strict_types=1);

if (!isset($_SERVER['argv'][1], $_SERVER['argv'][2])) {
    echo 'Usage: php benchmark.php <format> <iterations> [output-file]' . PHP_EOL;
    exit(1);
}

list(, $format, $iterations) = $_SERVER['argv'];

require_once 'bootstrap.php';

function benchmark(\Closure $f, $times = 10)
{
    $time = microtime(true);
    for ($i = 0; $i < $times; $i++) {
        $f();
    }

    return (microtime(true) - $time) / $times;
}

function createObject()
{
    $p = new \JMS\Serializer\Tests\Fixtures\Publisher('bar');
    $post = new \JMS\Serializer\Tests\Fixtures\BlogPost('FooooooooooooooooooooooBAR', new \JMS\Serializer\Tests\Fixtures\Author('Foo'), new \DateTime, $p);
    for ($i = 0; $i < 100; $i++) {
        $post->addComment(new \JMS\Serializer\Tests\Fixtures\Comment(new \JMS\Serializer\Tests\Fixtures\Author('foo'), 'foobar'));
    }

    return $post;
}

$serializer = \JMS\Serializer\SerializerBuilder::create()->build();
$obj = createObject();
$metrics = [];

$config = new \Blackfire\ClientConfiguration();
$config->setClientId('f46955db-6735-4f75-b89f-8f9999e023d8');
$config->setClientToken('1b01d44aaf9072b56d195be3fa53e1d3139974abffca76d11f1e1a4d45f0038d');

$blackfire = new \Blackfire\Client($config);
$config = new \Blackfire\Profile\Configuration();

// set the profile title
$config->setTitle('jms');

// compare the new profile with a reference
// takes the reference id as an argument

$config->setSamples($iterations);

$config->setReference(1);
//$config->setAsReference();

$probe = $blackfire->createProbe($config, false);

$f = function () use ($serializer, $obj, $format, $probe) {
    $probe->enable();
    $serializer->serialize($obj, $format);
    $probe->close();
};

// Load all necessary classes into memory.
$serializer->serialize($obj, $format);

printf('Benchmarking collection for format "%s".' . PHP_EOL, $format);
$metrics['benchmark-collection-' . $format] = benchmark($f, $iterations);

$profile = $blackfire->endProbe($probe);

echo "\n" . $profile->getUrl() . "\n";

$output = json_encode(['metrics' => $metrics]);

if (isset($_SERVER['argv'][3])) {
    file_put_contents($_SERVER['argv'][3], $output);
    echo "Done." . PHP_EOL;
} else {
    echo $output . PHP_EOL;
}

