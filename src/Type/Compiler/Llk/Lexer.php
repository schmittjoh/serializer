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

namespace JMS\Serializer\Type\Compiler\Llk;

use JMS\Serializer\Type\Compiler;
use JMS\Serializer\Type\Compiler\Exception\UnrecognizedToken;

/**
 * Class \JMS\Serializer\Type\Compiler\Llk\Lexer.
 *
 * Lexical analyser, i.e. split a string into a set of lexeme, i.e. tokens.
 */
final class Lexer
{
    /**
     * Lexer state.
     *
     * @var array
     */
    protected $_lexerState  = null;

    /**
     * Text.
     *
     * @var string
     */
    protected $_text        = null;

    /**
     * Tokens.
     *
     * @var array
     */
    protected $_tokens      = [];

    /**
     * Namespace stacks.
     *
     * @var \SplStack
     */
    protected $_nsStack     = null;

    /**
     * PCRE options.
     *
     * @var string
     */
    protected $_pcreOptions = null;



    /**
     * @param   array  $pragmas Pragmas.
     */
    public function __construct(array $pragmas = [])
    {
        if (!isset($pragmas['lexer.unicode']) || true === $pragmas['lexer.unicode']) {
            $this->_pcreOptions .= 'u';
        }

        return;
    }

    /**
     * Text tokenizer: splits the text in parameter in an ordered array of
     * tokens.
     *
     * @param string  $text   Text to tokenize.
     * @param array[]   $tokens Tokens to be returned.
     *
     * @return \Generator|array[]
     *
     * @throws UnrecognizedToken
     *
     * @psalm-return \Generator<int, array{token: string, value: string, length: int|false, namespace: array|string, keep: true, offset: int}>
     */
    public function lexMe(string $text, array $tokens): \Generator
    {
        $this->validateInputInUnicodeMode($text);

        $this->_text       = $text;
        $this->_tokens     = $tokens;
        $this->_nsStack    = null;
        $offset            = 0;
        $maxOffset         = strlen($this->_text);
        $this->_lexerState = 'default';
        $stack             = false;

        foreach ($this->_tokens as &$tokens) {
            $_tokens = [];

            foreach ($tokens as $fullLexeme => $regex) {
                if (false === strpos($fullLexeme, ':')) {
                    $_tokens[$fullLexeme] = [$regex, null];

                    continue;
                }

                [$lexeme, $namespace] = explode(':', $fullLexeme, 2);

                $stack |= ('__shift__' === substr($namespace, 0, 9));

                unset($tokens[$fullLexeme]);
                $_tokens[$lexeme] = [$regex, $namespace];
            }

            $tokens = $_tokens;
        }

        if (true === $stack) {
            $this->_nsStack = new \SplStack();
        }

        while ($offset < $maxOffset) {
            $nextToken = $this->nextToken($offset);

            if (null === $nextToken) {
                throw new Compiler\Exception\UnrecognizedToken(
                    'Unrecognized token "%s" at line 1 and column %d:' .
                    "\n" . '%s' . "\n" .
                    str_repeat(' ', mb_strlen(substr($text, 0, $offset))) . '↑',
                    0,
                    [
                        mb_substr(substr($text, $offset), 0, 1),
                        $offset + 1,
                        $text,
                    ],
                    1,
                    $offset
                );
            }

            if (true === $nextToken['keep']) {
                $nextToken['offset'] = $offset;
                yield $nextToken;
            }

            $offset += strlen($nextToken['value']);
        }

        yield [
            'token'     => 'EOF',
            'value'     => 'EOF',
            'length'    => 0,
            'namespace' => 'default',
            'keep'      => true,
            'offset'    => $offset,
        ];
    }

    /**
     * Compute the next token recognized at the beginning of the string.
     *
     * @param int  $offset Offset.
     *
     * @return (array|bool|int|string)[]|array[]|null
     *
     * @throws Compiler\Exception\Lexer
     *
     * @psalm-return array{token: string, value: string, length: int|false, namespace: array, keep: bool}|null
     */
    protected function nextToken(int $offset)
    {
        $tokenArray = &$this->_tokens[$this->_lexerState];

        $previousNamespace = null;
        foreach ($tokenArray as $lexeme => $bucket) {
            [$regex, $nextState] = $bucket;

            if (null === $nextState) {
                $nextState = $this->_lexerState;
            }

            $out = $this->matchLexeme($lexeme, $regex, $offset);

            if (null !== $out) {
                $out['namespace'] = $this->_lexerState;
                $out['keep']      = 'skip' !== $lexeme;

                if ($nextState !== $this->_lexerState) {
                    $shift = false;

                    if (null !== $this->_nsStack &&
                        0 !== preg_match('#^__shift__(?:\s*\*\s*(\d+))?$#', $nextState, $matches)) {
                        $i = isset($matches[1]) ? intval($matches[1]) : 1;

                        if ($i > ($c = count($this->_nsStack))) {
                            throw new Compiler\Exception\Lexer(
                                'Cannot shift namespace %d-times, from token ' .
                                '%s in namespace %s, because the stack ' .
                                'contains only %d namespaces.',
                                1,
                                [
                                    $i,
                                    $lexeme,
                                    $this->_lexerState,
                                    $c,
                                ]
                            );
                        }

                        while (1 <= $i--) {
                            $previousNamespace = $this->_nsStack->pop();
                        }

                        $nextState = $previousNamespace;
                        $shift     = true;
                    }

                    if (!isset($this->_tokens[$nextState])) {
                        throw new Compiler\Exception\Lexer(
                            'Namespace %s does not exist, called by token %s ' .
                            'in namespace %s.',
                            2,
                            [
                                $nextState,
                                $lexeme,
                                $this->_lexerState,
                            ]
                        );
                    }

                    if (null !== $this->_nsStack && false === $shift) {
                        $this->_nsStack[] = $this->_lexerState;
                    }

                    $this->_lexerState = $nextState;
                }

                return $out;
            }
        }

        return null;
    }

    /**
     * Check if a given lexeme is matched at the beginning of the text.
     *
     * @param string  $lexeme Name of the lexeme.
     * @param string  $regex  Regular expression describing the lexeme.
     * @param int     $offset Offset.
     *
     * @return (int|false|string)[]|null
     *
     * @throws Compiler\Exception\Lexer
     *
     * @psalm-return array{token: string, value: string, length: int|false}|null
     */
    protected function matchLexeme(string $lexeme, string $regex, int $offset)
    {
        $_regex = str_replace('#', '\#', $regex);
        $preg   = @preg_match(
            '#\G(?|' . $_regex . ')#' . $this->_pcreOptions,
            $this->_text,
            $matches,
            0,
            $offset
        );

        if (0 === $preg) {
            return null;
        }

        if (false === $preg) {
            throw new Compiler\Exception\InternalError(
                'Lexer encountered a PCRE error on a lexeme "%s", full regex: "%s". Please report this issue to the maintainers.',
                preg_last_error(),
                [$lexeme, $_regex]
            );
        }

        if ('' === $matches[0]) {
            throw new Compiler\Exception\Lexer(
                'A lexeme must not match an empty value, which is the ' .
                'case of "%s" (%s).',
                3,
                [$lexeme, $regex]
            );
        }

        return [
            'token'  => $lexeme,
            'value'  => $matches[0],
            'length' => mb_strlen($matches[0]),
        ];
    }

    private function validateInputInUnicodeMode(string $text): void
    {
        if (false !== strpos($this->_pcreOptions, 'u') && false === preg_match('##u', $text)) {
            throw new Compiler\Exception\Lexer(
                'Text is not valid utf-8 string, you probably need to switch "lexer.unicode" setting off.'
            );
        }
    }
}
