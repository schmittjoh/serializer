<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespaces');
$metadata->xmlRootName = 'test-object';
$metadata->xmlRootNamespace = 'http://example.com/namespace';

$metadata->registerNamespace('http://example.com/namespace');
$metadata->registerNamespace('http://schemas.google.com/g/2005', 'gd');
$metadata->registerNamespace('http://www.w3.org/2005/Atom', 'atom');

$pMetadata = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespaces', 'title');
$pMetadata->setType('string');
$pMetadata->xmlNamespace = "http://purl.org/dc/elements/1.1/";
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespaces', 'createdAt');
$pMetadata->setType('DateTime');
$pMetadata->xmlAttribute = true;
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespaces', 'etag');
$pMetadata->setType('string');
$pMetadata->xmlAttribute = true;
$pMetadata->xmlNamespace = 'http://schemas.google.com/g/2005';
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespaces', 'author');
$pMetadata->setType('string');
$pMetadata->xmlNamespace = 'http://www.w3.org/2005/Atom';
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\ObjectWithXmlNamespaces', 'language');
$pMetadata->setType('string');
$pMetadata->xmlAttribute = true;
$pMetadata->xmlNamespace = 'http://purl.org/dc/elements/1.1/';
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
