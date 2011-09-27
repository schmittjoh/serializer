<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Serializer\Handler;

use Metadata\MetadataFactoryInterface;

use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Construction\ObjectConstructorInterface;

class ObjectBasedCustomHandler implements SerializationHandlerInterface, DeserializationHandlerInterface
{
    private $objectConstructor;
    private $metadataFactory;

    public function __construct(ObjectConstructorInterface $objectConstructor, MetadataFactoryInterface $metadataFactory)
    {
        $this->objectConstructor = $objectConstructor;
        $this->metadataFactory = $metadataFactory;
    }

    public function serialize(VisitorInterface $visitor, $data, $type, &$handled)
    {
        if (!$data instanceof SerializationHandlerInterface) {
            return;
        }

        return $data->serialize($visitor, $data, $type, $handled);
    }

    public function deserialize(VisitorInterface $visitor, $data, $type, &$handled)
    {
        if (!class_exists($type)
            || !in_array('JMS\SerializerBundle\Serializer\Handler\DeserializationHandlerInterface', class_implements($type))
        ) {
            return;
        }

        $metadata = $this->metadataFactory->getMetadataForClass($type);
        $visitor->startVisitingObject($metadata, $data, $type);

        $instance = $visitor->getResult();
        $instance->deserialize($visitor, $data, $type, $handled);

        return $instance;
    }
}
