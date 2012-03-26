<?php

use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost');
$metadata->xmlRootName = 'blog-post';

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'title');
$pMetadata->type = 'string';
$pMetadata->groups = array('comments','post');
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'createdAt');
$pMetadata->type = 'DateTime';
$pMetadata->xmlAttribute = true;
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'published');
$pMetadata->type = 'boolean';
$pMetadata->serializedName = 'is_published';
$pMetadata->groups = array('post');
$pMetadata->xmlAttribute = true;
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'comments');
$pMetadata->type = 'ArrayCollection<JMS\SerializerBundle\Tests\Fixtures\Comment>';
$pMetadata->xmlCollection = true;
$pMetadata->xmlCollectionInline = true;
$pMetadata->xmlEntryName = 'comment';
$pMetadata->groups = array('comments');

$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\BlogPost', 'author');
$pMetadata->type = 'JMS\SerializerBundle\Tests\Fixtures\Author';
$pMetadata->groups = array('post');

$metadata->addPropertyMetadata($pMetadata);

return $metadata;