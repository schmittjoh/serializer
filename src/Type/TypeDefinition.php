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

namespace JMS\Serializer\Type;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class TypeDefinition
{
    private $name;
    private $params = array();

    public static function fromArray(array $type): self
    {
        $t = new self($type["name"], []);
        foreach ($type["params"] as $index => $param) {
            $t->params[$index] = $param instanceof TypeDefinition ? $param : self::fromArray($param);
        }
        return $t;
    }

    public static function toArray(TypeDefinition $type): array
    {
        $t = ['name' => $type->getName(), 'params' => []];

        foreach ($type->params as $index => $param) {
            $t['params'][$index] = self::toArray($param);
        }

        return $t;
    }

    public function __construct(string $name, array $params = array())
    {
        $this->name = $name;
        $this->params = $params;
    }

    public static function getUnknown(): self
    {
        return new self('UNKNOWN');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return TypeDefinition[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function hasParam(string $index): bool
    {
        return isset($this->params[$index]);
    }

    public function getParam(string $index): ?TypeDefinition
    {
        return isset($this->params[$index]) ? $this->params[$index] : null;
    }
}
