<?php

namespace JMS\SerializerExtraBundle\Tests\Serializer;

use JMS\SerializerExtraBundle\Tests\Fixtures\AllExcludedObject;

use JMS\SerializerExtraBundle\Tests\Fixtures\VersionedObject;
use JMS\SerializerExtraBundle\Serializer\Exclusion\DisjunctExclusionStrategy;
use JMS\SerializerExtraBundle\Serializer\Exclusion\VersionExclusionStrategy;
use JMS\SerializerExtraBundle\Serializer\Exclusion\NoneExclusionStrategy;
use JMS\SerializerExtraBundle\Serializer\Exclusion\AllExclusionStrategy;
use Symfony\Component\Serializer\Serializer;
use JMS\SerializerExtraBundle\Tests\Fixtures\CircularReferenceParent;
use JMS\SerializerExtraBundle\Tests\Fixtures\SimpleObject;
use JMS\SerializerExtraBundle\Serializer\Exclusion\ExclusionStrategyFactory;
use JMS\SerializerExtraBundle\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\SerializerExtraBundle\Serializer\Naming\AnnotatedNamingStrategy;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerExtraBundle\Serializer\AnnotatedNormalizer;

class AnnotatedNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizeAllExcludedObject()
    {
        $object = new AllExcludedObject();
        $normalizer = $this->getNormalizer();

        $this->assertEquals(array('bar' => 'bar'), $normalizer->normalize($object, null));
    }

    public function testNormalizeVersionedObject()
    {
        $object = new VersionedObject('name1', 'name2');

        $normalizer = $this->getNormalizer();
        $this->assertEquals(array('name' => 'name2'), $normalizer->normalize($object, null));

        $normalizer = $this->getNormalizer('0.1.0');
        $this->assertEquals(array('name' => 'name1'), $normalizer->normalize($object, null));

        $normalizer = $this->getNormalizer('1.1.0');
        $this->assertEquals(array('name' => 'name2'), $normalizer->normalize($object, null));
    }

    public function testNormalizeCircularReference()
    {
        $normalizer = $this->getNormalizer();
        $object = new CircularReferenceParent();

        $this->assertEquals(array(
            'collection' => array(
                array('name' => 'child1'),
                array('name' => 'child2'),
            ),
            'another_collection' => array(
                array('name' => 'child1'),
                array('name' => 'child2'),
            ),
        ), $normalizer->normalize($object, null));
    }

    public function testNormalize()
    {
        $normalizer = $this->getNormalizer();
        $object = new SimpleObject('foo', 'bar');

        $this->assertEquals(array('foo' => 'foo', 'moo' => 'bar', 'camel_case' => 'boo'), $normalizer->normalize($object, null));
    }

    protected function getNormalizer($version = null)
    {
        $reader = new AnnotationReader();
        $reader->setDefaultAnnotationNamespace('JMS\SerializerExtraBundle\Annotation\\');
        $reader->setAutoloadAnnotations(true);

        $propertyNamingStrategy = new AnnotatedNamingStrategy($reader, new CamelCaseNamingStrategy('_'));

        if (null === $version) {
            $strategies = array(
                'ALL'  => new AllExclusionStrategy($reader),
                'NONE' => new NoneExclusionStrategy($reader),
            );
        } else {
            $versionStrategy = new VersionExclusionStrategy($reader, $version);
            $strategies = array(
                'ALL'  => new DisjunctExclusionStrategy(array(
                    $versionStrategy, new AllExclusionStrategy($reader)
                )),
                'NONE' => new DisjunctExclusionStrategy(array(
                    $versionStrategy, new NoneExclusionStrategy($reader),
                )),
            );
        }
        $exclusionStrategyFactory = new ExclusionStrategyFactory($strategies);

        $normalizer = new AnnotatedNormalizer($reader, $propertyNamingStrategy, $exclusionStrategyFactory);

        $serializer = new Serializer();
        $serializer->addNormalizer($normalizer);

        return $normalizer;
    }
}