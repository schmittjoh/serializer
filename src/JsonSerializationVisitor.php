<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
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
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\AdvancedNamingStrategyInterface;

class JsonSerializationVisitor extends AbstractVisitor implements SerializationVisitorInterface
{
    private $options = 0;

    private $navigator;
    private $dataStack;
    private $data;

    public function setNavigator(GraphNavigatorInterface $navigator): void
    {
        $this->navigator = $navigator;
        $this->dataStack = new \SplStack;
    }

    public function visitNull($data, array $type, SerializationContext $context)
    {
        return null;
    }

    public function visitString(string $data, array $type, SerializationContext $context)
    {
        return $data;
    }

    public function visitBoolean(bool $data, array $type, SerializationContext $context)
    {
        return $data;
    }

    public function visitInteger(int $data, array $type, SerializationContext $context)
    {
        return $data;
    }

    public function visitDouble(float $data, array $type, SerializationContext $context)
    {
        return $data;
    }

    /**
     * @param array $data
     * @param array $type
     * @param SerializationContext $context
     * @return mixed
     */
    public function visitArray(array $data, array $type, SerializationContext $context)
    {
        $this->dataStack->push($data);

        $rs = isset($type['params'][1]) ? new \ArrayObject() : array();

        $isList = isset($type['params'][0]) && !isset($type['params'][1]);

        foreach ($data as $k => $v) {
            $v = $this->navigator->accept($v, $this->getElementType($type), $context);

            if (null === $v && $context->shouldSerializeNull() !== true) {
                continue;
            }

            if ($isList) {
                $rs[] = $v;
            } else {
                $rs[$k] = $v;
            }
        }

        $this->dataStack->pop();
        return $rs;
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, array $type, SerializationContext $context): void
    {
        $this->dataStack->push($this->data);
        $this->data = array();
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, SerializationContext $context)
    {
        $rs = $this->data;
        $this->data = $this->dataStack->pop();

        // Force JSON output to "{}" instead of "[]" if it contains either no properties or all properties are null.
        if (empty($rs)) {
            $rs = new \ArrayObject();
        }

        return $rs;
    }

    public function visitProperty(PropertyMetadata $metadata, $data, SerializationContext $context): void
    {
        $v = $this->accessor->getValue($data, $metadata);

        $v = $this->navigator->accept($v, $metadata->type, $context);
        if ((null === $v && $context->shouldSerializeNull() !== true)
            || (true === $metadata->skipWhenEmpty && ($v instanceof \ArrayObject || \is_array($v)) && 0 === count($v))
        ) {
            return;
        }

        if ($this->namingStrategy instanceof AdvancedNamingStrategyInterface) {
            $k = $this->namingStrategy->getPropertyName($metadata, $context);
        } else {
            $k = $this->namingStrategy->translateName($metadata);
        }

        if ($metadata->inline) {
            if (\is_array($v) || ($v instanceof \ArrayObject)) {
                $this->data = array_merge($this->data, (array)$v);
            }
        } else {
            $this->data[$k] = $v;
        }
    }

    /**
     * Checks if some data key exists.
     *
     * @param string $key
     * @return boolean
     */
    public function hasData($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Allows you to replace existing data on the current object/root element.
     *
     * @param string $key
     * @param integer|float|boolean|string|array|null $value This value must either be a regular scalar, or an array.
     *                                                       It must not contain any objects anymore.
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function getResult($data)
    {
        $result = @json_encode($data, $this->options);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $result;

            case JSON_ERROR_UTF8:
                throw new \RuntimeException('Your data could not be encoded because it contains invalid UTF8 characters.');

            default:
                throw new \RuntimeException(sprintf('An error occurred while encoding your data (error code %d).', json_last_error()));
        }
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = (integer)$options;
    }
}
