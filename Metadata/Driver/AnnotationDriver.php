<?php

namespace JMS\SerializerBundle\Metadata\Driver;

use Annotations\ReaderInterface;
use JMS\SerializerBundle\Annotation\Type;
use JMS\SerializerBundle\Annotation\Exclude;
use JMS\SerializerBundle\Annotation\Expose;
use JMS\SerializerBundle\Annotation\SerializedName;
use JMS\SerializerBundle\Annotation\Until;
use JMS\SerializerBundle\Annotation\Since;
use JMS\SerializerBundle\Annotation\ExclusionPolicy;
use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;

class AnnotationDriver implements DriverInterface
{
    private $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($name = $class->getName());
        foreach ($this->reader->getClassAnnotations($class) as $annot) {
            if ($annot instanceof ExclusionPolicy) {
                $classMetadata->setExclusionPolicy($annot->getStrategy());
            }
        }

        foreach ($class->getProperties() as $property) {
            if ($property->getDeclaringClass()->getName() !== $name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($name, $property->getName());
            foreach ($this->reader->getPropertyAnnotations($property) as $annot) {
                if ($annot instanceof Since) {
                    $propertyMetadata->setSinceVersion($annot->getVersion());
                } else if ($annot instanceof Until) {
                    $propertyMetadata->setUntilVersion($annot->getVersion());
                } else if ($annot instanceof SerializedName) {
                    $propertyMetadata->setSerializedName($annot->getName());
                } else if ($annot instanceof Expose) {
                    $propertyMetadata->setExposed(true);
                } else if ($annot instanceof Exclude) {
                    $propertyMetadata->setExcluded(true);
                } else if ($annot instanceof Type) {
                    $propertyMetadata->setType($annot->getName());
                }
            }
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }
}