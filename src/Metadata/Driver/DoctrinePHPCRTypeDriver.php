<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

use Doctrine\Persistence\Mapping\ClassMetadata as DoctrineClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * This class decorates any other driver. If the inner driver does not provide a
 * a property type, the decorator will guess based on Doctrine 2 metadata.
 */
class DoctrinePHPCRTypeDriver extends AbstractDoctrineTypeDriver
{
    protected function hideProperty(DoctrineClassMetadata $doctrineMetadata, PropertyMetadata $propertyMetadata): bool
    {
        return 'lazyPropertiesDefaults' === $propertyMetadata->name
            || $doctrineMetadata->parentMapping === $propertyMetadata->name
            || $doctrineMetadata->node === $propertyMetadata->name;
    }

    protected function setPropertyType(DoctrineClassMetadata $doctrineMetadata, PropertyMetadata $propertyMetadata): void
    {
        $propertyName = $propertyMetadata->name;
        if (
            $doctrineMetadata->hasField($propertyName)
            && ($typeOfFiled = $doctrineMetadata->getTypeOfField($propertyName))
            && ($fieldType = $this->normalizeFieldType($typeOfFiled))
        ) {
            $field = $doctrineMetadata->getFieldMapping($propertyName);
            if (!empty($field['multivalue'])) {
                $fieldType = 'array';
            }

            $propertyMetadata->setType($this->typeParser->parse($fieldType));
        } elseif ($doctrineMetadata->hasAssociation($propertyName)) {
            try {
                $targetEntity = $doctrineMetadata->getAssociationTargetClass($propertyName);
            } catch (\Throwable $e) {
                return;
            }

            if (null === $this->tryLoadingDoctrineMetadata($targetEntity)) {
                return;
            }

            if (!$doctrineMetadata->isSingleValuedAssociation($propertyName)) {
                $targetEntity = sprintf('ArrayCollection<%s>', $targetEntity);
            }

            $propertyMetadata->setType($this->typeParser->parse($targetEntity));
        }
    }
}
