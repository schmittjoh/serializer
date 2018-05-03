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

namespace JMS\Serializer\Ordering;

final class CustomPropertyOrderingStrategy implements PropertyOrderingInterface
{
    /** @var int[] property => weight */
    private $ordering;

    /**
     * @param int[] $ordering property => weight
     */
    public function __construct(array $ordering)
    {
        $this->ordering = $ordering;
    }

    /**
     * {@inheritdoc}
     */
    public function order(array $properties) : array
    {
        $currentSorting = $properties ? array_combine(array_keys($properties), range(1, \count($properties))) : [];

        uksort($properties, function ($a, $b) use ($currentSorting) {
            $existsA = isset($this->ordering[$a]);
            $existsB = isset($this->ordering[$b]);

            if (!$existsA && !$existsB) {
                return $currentSorting[$a] - $currentSorting[$b];
            }

            if (!$existsA) {
                return 1;
            }

            if (!$existsB) {
                return -1;
            }

            return $this->ordering[$a] < $this->ordering[$b] ? -1 : 1;
        });

        return $properties;
    }
}
