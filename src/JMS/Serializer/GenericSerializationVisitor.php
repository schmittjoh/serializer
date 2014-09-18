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

namespace JMS\Serializer;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

abstract class GenericSerializationVisitor extends AbstractVisitor
{
    private $navigatorStack;
    private $navigator;
    private $rootStack;
    private $root;
    private $dataStackStack;
    private $dataStack;
    private $data;

    public function __construct(PropertyNamingStrategyInterface $namingStrategy)
    {
        parent::__construct($namingStrategy);
        $this->navigatorStack = new \SplStack;
        $this->dataStackStack = new \SplStack;
        $this->rootStack = new \SplStack;
    }

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigatorStack->push($this->navigator);
        $this->dataStackStack->push($this->dataStack);
        $this->rootStack->push($this->root);

        $this->navigator = $navigator;
        $this->dataStack = new \SplStack;
        $this->root = null;
    }

    public function endNavigator()
    {
        $this->navigator = $this->navigatorStack->pop();
        $this->dataStack = $this->dataStackStack->pop();
        $this->root = $this->rootStack->pop();
    }

    /**
     * @return GraphNavigator
     */
    public function getNavigator()
    {
        return $this->navigator;
    }

    public function visitNull($data, array $type, Context $context)
    {
        return null;
    }

    public function visitString($data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (string) $data;
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (boolean) $data;
    }

    public function visitInteger($data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (int) $data;
    }

    public function visitDouble($data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (float) $data;
    }

    /**
     * @param array $data
     * @param array $type
     */
    public function visitArray($data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = array();
            $rs = &$this->root;
        } else {
            $rs = array();
        }

        foreach ($data as $k => $v) {
            $v = $this->navigator->accept($v, $this->getElementType($type), $context);

            if (null === $v && (!is_string($k) || !$context->shouldSerializeNull())) {
                continue;
            }

            $rs[$k] = $v;
        }

        return $rs;
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = new \stdClass;
        }

        $this->dataStack->push($this->data);
        $this->data = array();
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        $rs = $this->data;
        $this->data = $this->dataStack->pop();

        if ($this->root instanceof \stdClass && 0 === $this->dataStack->count()) {
            $this->root = $rs;
        }

        return $rs;
    }

    public function visitProperty(PropertyMetadata $metadata, $data, Context $context)
    {
        $v = $metadata->getValue($data);

        $v = $this->navigator->accept($v, $metadata->type, $context);
        if (null === $v && !$context->shouldSerializeNull()) {
            return;
        }

        $k = $this->namingStrategy->translateName($metadata);

        if ($metadata->inline) {
            if (is_array($v)) {
                $this->data = array_merge($this->data, $v);
            }
        } else {
            $this->data[$k] = $v;
        }
    }

    /**
     * Allows you to add additional data to the current object/root element.
     *
     * @param string $key
     * @param scalar|array $value This value must either be a regular scalar, or an array.
     *                            It must not contain any objects anymore.
     */
    public function addData($key, $value)
    {
        if (isset($this->data[$key])) {
            throw new InvalidArgumentException(sprintf('There is already data for "%s".', $key));
        }

        $this->data[$key] = $value;
    }

    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param array|\ArrayObject $data the passed data must be understood by whatever encoding function is applied later.
     */
    public function setRoot($data)
    {
        $this->root = $data;
    }
}
