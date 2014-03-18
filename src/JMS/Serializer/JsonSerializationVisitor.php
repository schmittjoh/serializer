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

class JsonSerializationVisitor extends GenericSerializationVisitor
{
    private $options = 0;

    public function getResult()
    {
        $result = @json_encode($this->getRoot(), $this->options);

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
