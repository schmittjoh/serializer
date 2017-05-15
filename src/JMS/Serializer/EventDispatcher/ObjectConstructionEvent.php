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

namespace JMS\Serializer\EventDispatcher;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;

/**
 * ObjectConstructionEvent
 * @author Robert Schönthal <robert.schoenthal@gmail.com>
 */
class ObjectConstructionEvent extends Event
{
    /**
     * @var ClassMetadata
     */
    private $metadata;

    private $object;

    public function __construct(Context $context, array $type, ClassMetadata $metadata)
    {
        parent::__construct($context, $type);
        $this->metadata = $metadata;
    }

    /**
     * @return ClassMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }
}
