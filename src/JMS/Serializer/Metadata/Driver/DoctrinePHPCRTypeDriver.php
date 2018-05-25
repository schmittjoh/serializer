<?php

namespace JMS\Serializer\Metadata\Driver;

use Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * This class decorates any other driver. If the inner driver does not provide a
 * a property type, the decorator will guess based on Doctrine 2 metadata.
 */
class DoctrinePHPCRTypeDriver extends AbstractDoctrineTypeDriver
{
    /**
     * @param DoctrineClassMetadata $doctrineMetadata
     * @param PropertyMetadata $propertyMetadata
     */
    protected function hideProperty(DoctrineClassMetadata $doctrineMetadata, PropertyMetadata $propertyMetadata)
    {
        return 'lazyPropertiesDefaults' === $propertyMetadata->name
            || $doctrineMetadata->parentMapping === $propertyMetadata->name
            || $doctrineMetadata->node === $propertyMetadata->name;
    }

    protected function setPropertyType(DoctrineClassMetadata $doctrineMetadata, PropertyMetadata $propertyMetadata)
    {
        $propertyName = $propertyMetadata->name;
        if ($doctrineMetadata->hasField($propertyName) && $fieldType = $this->normalizeFieldType($doctrineMetadata->getTypeOfField($propertyName))) {
            $field = $doctrineMetadata->getFieldMapping($propertyName);
            if (!empty($field['multivalue'])) {
                $fieldType = 'array';
            }

            $propertyMetadata->setType($fieldType);
        } elseif ($doctrineMetadata->hasAssociation($propertyName)) {
            try {
                $targetEntity = $doctrineMetadata->getAssociationTargetClass($propertyName);
            } catch (\Exception $e) {
                return;
            }

            if (null === $this->tryLoadingDoctrineMetadata($targetEntity)) {
                return;
            }

            if (!$doctrineMetadata->isSingleValuedAssociation($propertyName)) {
                $targetEntity = "ArrayCollection<{$targetEntity}>";
            }

            $propertyMetadata->setType($targetEntity);
        }
    }
}
