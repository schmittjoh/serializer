<?php

namespace JMS\Serializer\Metadata;

interface ClassMetadataUpdaterInterface
{
    /**
     * @param ClassMetadata $classClassMetadata
     *
     * @throws \JMS\Serializer\Exception\RuntimeException
     */
    public function update(ClassMetadata $classClassMetadata);
}
