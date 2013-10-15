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

namespace JMS\Serializer\Twig;

use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;

/**
 * Serializer helper twig extension
 *
 * Basically provides access to JMSSerializer from Twig
 */
class SerializerExtension extends \Twig_Extension
{
    protected $serializer;

    public function getName()
    {
        return 'jms_serializer';
    }

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getFilters()
    {
        return array(
            'serialize'      => new \Twig_Filter_Method($this, 'serialize'),
        );
    }

    public function getFunctions()
    {
        return array(
            'serialization_context' => new \Twig_Function_Method($this, 'createContext'),
        );
    }

    /**
     * Creates the serialization context
     *
     * @return SerializationContext
     */
    public function createContext()
    {
        return SerializationContext::create();
    }

    /**
     * @param object $object
     * @param string $type
     * @param SerializationContext $context
     */
    public function serialize($object, $type = 'json', SerializationContext $context = null)
    {
        return $this->serializer->serialize($object, $type, $context);
    }
}
