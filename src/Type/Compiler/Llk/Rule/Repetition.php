<?php

declare(strict_types=1);

/**
 * Hoa
 *
 *
 *
 *
 * BSD 3-Clause License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its
 *    contributors may be used to endorse or promote products derived from
 *    this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace JMS\Serializer\Type\Compiler\Llk\Rule;

use JMS\Serializer\Type\Compiler;
use JMS\Serializer\Type\Compiler\Llk\Rule;

/**
 * Class \JMS\Serializer\Type\Compiler\Llk\Rule\Repetition.
 *
 * The repetition rule.
 */
final class Repetition extends Rule
{
    /**
     * Minimum bound.
     *
     * @var int
     */
    protected $_min = 0;

    /**
     * Maximum bound.
     *
     * @var int
     */
    protected $_max = 0;



    /**
     * @param   mixed  $name     Name.
     * @param   int     $min      Minimum bound.
     * @param   int     $max      Maximum bound.
     * @param   mixed   $children Children.
     * @param   string  $nodeId   Node ID.
     */
    public function __construct($name, int $min, int $max, $children, $nodeId)
    {
        parent::__construct($name, $children, $nodeId);

        $min = max(0, (int) $min);
        $max = max(-1, (int) $max);

        if (-1 !== $max && $min > $max) {
            throw new Compiler\Exception\Rule(
                'Cannot repeat with a min (%d) greater than max (%d).',
                0,
                [$min, $max]
            );
        }

        $this->_min = $min;
        $this->_max = $max;

        return;
    }

    /**
     * Get minimum bound.
     */
    public function getMin(): int
    {
        return $this->_min;
    }

    /**
     * Get maximum bound.
     */
    public function getMax(): int
    {
        return $this->_max;
    }

    /**
     * Check whether the maximum repetition is unbounded.
     */
    public function isInfinite(): bool
    {
        return -1 === $this->getMax();
    }
}
