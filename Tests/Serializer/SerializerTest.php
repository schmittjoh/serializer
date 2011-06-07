<?php

namespace JMS\SerializerBundle\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\MetadataFactory;
use JMS\SerializerBundle\Serializer\UnserializeInstanceCreator;
use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use JMS\SerializerBundle\Serializer\Exclusion\AllExclusionStrategy;
use JMS\SerializerBundle\Serializer\Normalizer\PropertyBasedNormalizer;
use JMS\SerializerBundle\Tests\Fixtures\Comment;
use JMS\SerializerBundle\Tests\Fixtures\Author;
use JMS\SerializerBundle\Tests\Fixtures\AuthorList;
use JMS\SerializerBundle\Tests\Fixtures\BlogPost;
use JMS\SerializerBundle\Tests\Fixtures\NoopNormalizer;
use JMS\SerializerBundle\Serializer\Normalizer\ArrayCollectionNormalizer;
use JMS\SerializerBundle\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\SerializerBundle\Serializer\SerializerFactory;
use JMS\SerializerBundle\Serializer\Exclusion\NoneExclusionStrategy;
use JMS\SerializerBundle\Serializer\Exclusion\ExclusionStrategyFactory;
use JMS\SerializerBundle\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\SerializerBundle\Serializer\Naming\AnnotatedNamingStrategy;
use JMS\SerializerBundle\Serializer\Normalizer\NativePhpTypeNormalizer;
use JMS\SerializerBundle\Serializer\Serializer;
use JMS\SerializerBundle\Serializer\Encoder\JsonEncoder;
use JMS\SerializerBundle\Serializer\Encoder\XmlEncoder;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $serializer = $this->getSerializer();

        $post = new BlogPost('Foo', new Author('Bar'));
        $normalized = $serializer->normalize($post);

        $this->assertTrue(isset($normalized['created_at']));
        unset($normalized['created_at']);

        $this->assertEquals(array(
            'title' => 'Foo',
            'published' => false,
            'author' => array(
                'full_name' => 'Bar',
            ),
            'comments' => array(),
        ), $normalized);

        $noop = new NoopNormalizer();
        $serializer = $this->getSerializer(null, null, array($noop));

        $list = new AuthorList();
        $list->add(new Author('Bar'));

        $normalized = $serializer->normalize($list);
        $this->assertEquals(array(), $normalized);

        $normalized = $serializer->normalize(array('foo'));
        $this->assertEquals(array('foo'), $normalized);
    }

    public function testDenormalize()
    {
        $serializer = $this->getSerializer();

        $post = new BlogPost('Foo', new Author('Bar'));
        $post->addComment(new Comment(new Author('Johannes'), 'FooBar'));
        $normalized = $serializer->normalize($post);

        $post2 = $serializer->denormalize($normalized, 'JMS\SerializerBundle\Tests\Fixtures\BlogPost');
        $this->assertEquals($post, $post2);
    }

    public function testSerializeUnserialize()
    {
        $serializer = $this->getSerializer();

        $post = new BlogPost('foo', new Author('bar'));
        $post->setPublished();
        $post->addComment(new Comment(new Author('foo'), 'bar'));
        $post->addComment(new Comment(new Author('bar'), 'foo'));

        $serialized = $serializer->serialize($post, 'xml');
        $post2 = $serializer->deserialize($serialized, 'JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'xml');
        $this->assertEquals($post, $post2);

        $serialized = $serializer->serialize($post, 'json');
        $post2 = $serializer->deserialize($serialized, 'JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'json');
        $this->assertEquals($post, $post2);
    }

    private function getSerializer($propertyNamingStrategy = null, $encoders = null, $customNormalizers = null)
    {
        if (null === $propertyNamingStrategy) {
            $propertyNamingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        }

        if (null === $encoders) {
            $encoders = array(
                'xml'  => new XmlEncoder(),
                'json' => new JsonEncoder(),
            );
        }

        if (null === $customNormalizers) {
            $customNormalizers = array(
                new ArrayCollectionNormalizer(),
            );
        }

        $exclusionStrategyFactory = new ExclusionStrategyFactory(array(
            'ALL'  => new AllExclusionStrategy(),
            'NONE' => new NoneExclusionStrategy(),
        ));

        return new Serializer(
            new NativePhpTypeNormalizer(),
            new PropertyBasedNormalizer(new MetadataFactory(new AnnotationDriver(new AnnotationReader())), $propertyNamingStrategy, new UnserializeInstanceCreator(), $exclusionStrategyFactory),
            $customNormalizers,
            $encoders
        );
    }
}
