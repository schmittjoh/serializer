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

namespace JMS\Serializer\Type;

use Hoa\Exception\Exception;
use Hoa\Visitor\Visit;
use JMS\Serializer\Type\Exception\SyntaxError;

final class Parser implements ParserInterface
{
    /** @var InnerParser */
    private $parser;

    /** @var Visit */
    private $visitor;

    public function __construct()
    {
        $this->parser = new InnerParser();
        $this->visitor = new TypeVisitor();
    }

    public function parse(string $type) : array
    {
        try {
            $ast = $this->parser->parse($type, 'type');

            return $this->visitor->visit($ast);
        } catch (Exception $e) {
            throw new SyntaxError($e->getMessage(), 0, $e);
        }
    }
}
