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

namespace JMS\Serializer\Handler;

use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\ClassNamingStrategy;
use JMS\Serializer\Naming\ClassTypeNamingStrategyInterface;
use JMS\Serializer\NavigatorContext;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * Handler for object. This handler will store an object type on serialization and then use it to detect actual class
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ObjectHandler implements SubscribingHandlerInterface
{
    private $namingStrategy;
    private $typeAttribute;

    public function __construct(ClassTypeNamingStrategyInterface $namingStrategy = null, $typeAttribute = 'type')
    {
        $this->namingStrategy = $namingStrategy ?: new ClassNamingStrategy();
        $this->typeAttribute = $typeAttribute;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = array();
        foreach (array('json', 'xml') as $format) {
            $methods[] = array(
                'type' => 'object',
                'format' => $format,
            );
        }

        return $methods;
    }

    private function getTypeAttributeForType(array $type)
    {
        return (isset($type['params'][0]) && $type['params'][0]) ? $type['params'][0] : $this->typeAttribute;
    }

    public function serializeObjectToJson(JsonSerializationVisitor $visitor, $obj, array $type, NavigatorContext $context)
    {
        $result = $this->serializeObject($visitor, $obj, $context);
        $result[$this->getTypeAttributeForType($type)] = $this->namingStrategy->classToType(get_class($obj));

        return $result;
    }

    public function serializeObjectToXml(XmlSerializationVisitor $visitor, $obj, array $type, NavigatorContext $context)
    {
        $result = $this->serializeObject($visitor, $obj, $context);
        $visitor->getCurrentNode()->setAttribute($this->getTypeAttributeForType($type), $this->namingStrategy->classToType(get_class($obj)));

        return $result;
    }

    private function serializeObject(VisitorInterface $visitor, $obj, NavigatorContext $context)
    {
        if (!is_object($obj)) {
            throw new InvalidArgumentException(sprintf('Expected object but got "%s"', gettype($obj)));
        }

        /** @var $navigator \JMS\Serializer\GraphNavigator */
        $navigator = $visitor->getNavigator();
        $context->stopVisiting($obj);

        $class = get_class($obj);
        $result = $navigator->accept($obj, array('name' => $class), $visitor);
        $context->startVisiting($obj);

        return $result;
    }

    public function deserializeObjectFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->deserializeObject($visitor, $data, $type);
    }

    public function deserializeObjectFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->deserializeObject($visitor, $data, $type);
    }

    private function deserializeObject(VisitorInterface $visitor, $data, array $type)
    {
        return $visitor->getNavigator()->accept(
            $data,
            array('name' => $this->namingStrategy->typeToClass((string) $data[$this->getTypeAttributeForType($type)])),
            $visitor
        );
    }

}
