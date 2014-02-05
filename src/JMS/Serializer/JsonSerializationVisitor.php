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
use JMS\Serializer\Exception\JsonErrorException;

class JsonSerializationVisitor extends GenericSerializationVisitor
{
    private $options = 0;

    public function getResult()
    {
        $root = $this->getRoot();
        $options = $this->options;

        // Errors intentionally suppressed here to deliver consistency across
        // PHP versions (PHP 5.5 does not emit warnings when encoding fails)
        $result = @json_encode($root, $options);

        // When JSON serialization fails an exception will be thrown
        if (false === $result) {
            throw JsonErrorException::fromLastError();
        }

        return $result;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = (integer) $options;
    }

    public function visitArray($data, array $type, Context $context)
    {
        $result = parent::visitArray($data, $type, $context);

        if (null !== $this->getRoot() && isset($type['params'][1]) && 0 === count($result)) {
            // ArrayObject is specially treated by the json_encode function and
            // serialized to { } while a mere array would be serialized to [].
            return new \ArrayObject();
        }

        return $result;
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        $rs = parent::endVisitingObject($metadata, $data, $type, $context);

        // Force JSON output to "{}" instead of "[]" if it contains either no properties or all properties are null.
        if (empty($rs)) {
            $rs = new \ArrayObject();

            if (array() === $this->getRoot()) {
                $this->setRoot(clone $rs);
            }
        }

        return $rs;
    }
}
