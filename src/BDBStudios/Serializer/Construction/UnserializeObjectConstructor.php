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

namespace BDBStudios\Serializer\Construction;

use BDBStudios\Serializer\VisitorInterface;
use BDBStudios\Serializer\Metadata\ClassMetadata;
use BDBStudios\Serializer\DeserializationContext;

class UnserializeObjectConstructor implements ObjectConstructorInterface
{
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        return unserialize(sprintf('O:%d:"%s":0:{}', strlen($metadata->name), $metadata->name));
    }
}
