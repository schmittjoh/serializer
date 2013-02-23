<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Tests;

use Symfony\Component\DependencyInjection\Compiler\ResolveParameterPlaceHoldersPass;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\IdentityTranslator;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;
use JMS\Serializer\JMSSerializerBundle;
use JMS\Serializer\DependencyInjection\JMSSerializerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use JMS\Serializer\Tests\Fixtures\Comment;
use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\BlogPost;

/**
 * @group performance
 */
class PerformanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getFormats
     */
    public function testSerializationPerformance($format)
    {
        $serializer = $this->getSerializer();
        $testData   = $this->getTestCollection();

        $time = microtime(true);
        for ($i=0,$c=10; $i<$c; $i++) {
            $serializer->serialize($testData, $format);
        }
        $time = microtime(true) - $time;

        $this->printResults("serialization ($format)", $time, $c);
    }

    public function getFormats()
    {
        return array(array('json'), array('xml'));
    }

    private function getTestCollection()
    {
        $collection = array();
        for ($i=0; $i<50; $i++) {
            $collection[] = $this->getTestObject();
        }

        return $collection;
    }

    private function getTestObject()
    {
        $post = new BlogPost('FooooooooooooooooooooooBAR', new Author('Foo'), new \DateTime);
        for ($i=0; $i<10; $i++) {
            $post->addComment(new Comment(new Author('foo'), 'foobar'));
        }

        return $post;
    }

    private function getSerializer()
    {
        $container = new ContainerBuilder();
        $container->set('annotation_reader', new AnnotationReader());
        $container->set('translator', new IdentityTranslator(new MessageSelector()));
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.cache_dir', sys_get_temp_dir().'/serializer');
        $container->setParameter('kernel.bundles', array());
        $extension = new JMSSerializerExtension();
        $extension->load(array(array()), $container);

        $bundle = new JMSSerializerBundle();
        $bundle->build($container);

        $container->getCompilerPassConfig()->setOptimizationPasses(array(
            new ResolveDefinitionTemplatesPass(),
            new ResolveParameterPlaceHoldersPass(),
        ));
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container->get('serializer');
    }

    private function printResults($test, $time, $iterations)
    {
        if (0 == $iterations) {
            throw new InvalidArgumentException('$iterations cannot be zero.');
        }

        $title = $test." results:\n";
        $iterationsText = sprintf("Iterations:         %d\n", $iterations);
        $totalTime      = sprintf("Total Time:         %.3f s\n", $time);
        $iterationTime  = sprintf("Time per iteration: %.3f ms\n", $time/$iterations * 1000);

        $max = max(strlen($title), strlen($iterationTime)) - 1;

        echo "\n".str_repeat('-', $max)."\n";
        echo $title;
        echo str_repeat('=', $max)."\n";
        echo $iterationsText;
        echo $totalTime;
        echo $iterationTime;
        echo str_repeat('-', $max)."\n";
    }
}