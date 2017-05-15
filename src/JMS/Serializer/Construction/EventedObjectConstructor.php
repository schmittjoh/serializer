<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Construction;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\ObjectConstructionEvent;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;

/**
 * EventedObjectConstructor
 * @author Robert Schönthal <robert.schoenthal@gmail.com>
 */
class EventedObjectConstructor implements ObjectConstructorInterface
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var ObjectConstructorInterface
     */
    private $fallbackConstructor;

    public function __construct(EventDispatcher $eventDispatcher, ObjectConstructorInterface $fallbackConstructor)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->fallbackConstructor = $fallbackConstructor;
    }

    /**
     * @inheritdoc
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        $event = new ObjectConstructionEvent($context, $data, $metadata);
        $this->eventDispatcher->dispatch(Events::OBJECT_CONSTRUCTION, $metadata->name, $context->getFormat(), $event);

        if ($object = $event->getObject()) {
            return $object;
        }

        return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
    }
}
