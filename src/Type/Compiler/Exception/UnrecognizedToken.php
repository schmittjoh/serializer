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

namespace JMS\Serializer\Type\Compiler\Exception;

use JMS\Serializer\Type\Compiler\Exception;

/**
 * Class \JMS\Serializer\Type\Compiler\Exception\UnrecognizedToken.
 *
 * Extending the \JMS\Serializer\Type\Compiler\Exception class.
 */
class UnrecognizedToken extends Exception
{
    /**
     * Column.
     *
     * @var int
     */
    protected $column = 0;



    /**
     * Override line and add column support.
     *
     * @param   string  $message Formatted message.
     * @param   int     $code    Code (the ID).
     * @param   array   $arg     RaiseError string arguments.
     * @param   int     $line    Line.
     * @param   int     $column  Column.
     */
    public function __construct(string $message, int $code, array $arg, int $line, int $column)
    {
        parent::__construct($message, $code, $arg);

        $this->line   = $line;
        $this->column = $column;

        return;
    }

    /**
     * Get column.
     */
    public function getColumn(): int
    {
        return $this->column;
    }
}
