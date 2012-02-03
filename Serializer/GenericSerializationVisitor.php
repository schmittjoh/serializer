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

namespace JMS\SerializerBundle\Serializer;

use JMS\SerializerBundle\Metadata\ClassMetadata;

use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Serializer\Naming\PropertyNamingStrategyInterface;

abstract class GenericSerializationVisitor extends AbstractSerializationVisitor
{
    private $navigator;
    private $root;
    private $dataStack;
    private $data;

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->root = null;
        $this->dataStack = new \SplStack;
    }

    public function getNavigator()
    {
        return $this->navigator;
    }

    public function visitString($data, $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return $data;
    }

    public function visitBoolean($data, $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return $data;
    }

    public function visitInteger($data, $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return $data;
    }

    public function visitDouble($data, $type)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return $data;
    }

    public function visitArray($data, $type)
    {
        if (null === $this->root) {
            $this->root = array();
            $rs = &$this->root;
        } else {
            $rs = array();
        }

        foreach ($data as $k => $v) {
            $v = $this->navigator->accept($v, null, $this);

            if (null === $v) {
                continue;
            }

            $rs[$k] = $v;
        }

        return $rs;
    }

    public function visitTraversable($data, $type)
    {
        return $this->visitArray($data, $type);
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, $type)
    {
        if (null === $this->root) {
            $this->root = new \stdClass;
        }

        $this->dataStack->push($this->data);
        $this->data = array();
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, $type)
    {
        $rs = $this->data;
        $this->data = $this->dataStack->pop();

        if ($this->root instanceof \stdClass && 0 === $this->dataStack->count()) {
            $this->root = $rs;
        }

        return $rs;
    }

    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        $v = (null === $metadata->getter ? $metadata->reflection->getValue($data)
                : $data->{$metadata->getter}());

        $v = $this->navigator->accept($v, null, $this);
        if (null === $v) {
            return;
        }

        $k = $this->namingStrategy->translateName($metadata);

        if ($metadata->inline && is_array($v)) {
            $this->data = array_merge($this->data, $v);
        } else {
            $this->data[$k] = $v;
        }
    }

    public function visitPropertyUsingCustomHandler(PropertyMetadata $metadata, $object)
    {
        // TODO
        return false;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function setRoot($data)
    {
        $this->root = $data;
    }
}
