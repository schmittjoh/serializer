<?php

use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost');
$metadata->xmlRootName = 'blog-post';

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'title');
$pMetadata->setType('string');
$pMetadata->groups = array('comments','post');
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'createdAt');
$pMetadata->setType('DateTime');
$pMetadata->xmlAttribute = true;
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'published');
$pMetadata->setType('boolean');
$pMetadata->serializedName = 'is_published';
$pMetadata->groups = array('post');
$pMetadata->xmlAttribute = true;
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'comments');
$pMetadata->setType('ArrayCollection<JMS\SerializerBundle\Tests\Fixtures\Comment>');
$pMetadata->xmlCollection = true;
$pMetadata->xmlCollectionInline = true;
$pMetadata->xmlEntryName = 'comment';
$pMetadata->groups = array('comments');

$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'author');
$pMetadata->setType('JMS\SerializerBundle\Tests\Fixtures\Author');
$pMetadata->groups = array('post');

$metadata->addPropertyMetadata($pMetadata);

return $metadata;