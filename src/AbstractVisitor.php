<?php

declare(strict_types=1);

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
use JMS\Serializer\Type\TypeDefinition;

/**
 * @internal
 */
abstract class AbstractVisitor implements VisitorInterface
{
    /**
     * @var GraphNavigatorInterface
     */
    protected $navigator;

    public function setNavigator(GraphNavigatorInterface $navigator): void
    {
        $this->navigator = $navigator;
    }

    public function prepare($data)
    {
        return $data;
    }

    protected function getElementType(TypeDefinition $type): ?TypeDefinition
    {
        if ($type->hasParam('0')) {
            return null;
        }

        if ($type->hasParam('1')) {
            return $type->getParam('1');
        } else {
            return $type->getParam('0');
        }
    }
}
