<?php
/**
 * Hoa
 *
 *
 * @license
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
 *
 */

namespace JMS\Serializer\Type\Compiler\Llk\Rule;

use JMS\Serializer\Type\Compiler;
use Hoa\File;

/**
 * Class \JMS\Serializer\Type\Compiler\Llk\Rule\Token.
 *
 * The token rule.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
final class Token extends Rule
{
    /**
     * LL(k) compiler of hoa://Library/Regex/Grammar.pp.
     *
     * @var \JMS\Serializer\Type\Compiler\Llk\Parser|null
     */
    protected static $_regexCompiler = null;

    /**
     * Token name.
     *
     * @var string|null
     */
    protected $_tokenName            = null;

    /**
     * Namespace.
     *
     * @var string|null
     */
    protected $_namespace            = null;

    /**
     * Token representation.
     *
     * @var string|null
     */
    protected $_regex                = null;

    /**
     * AST of the regex.
     *
     * @var \JMS\Serializer\Type\Compiler\Llk\TreeNode
     */
    protected $_ast;

    /**
     * Token value.
     *
     * @var string|null
     */
    protected $_value                = null;

    /**
     * Whether the token is kept or not in the AST.
     *
     * @var bool
     */
    protected $_kept                 = false;

    /**
     * Unification index.
     *
     * @var int
     */
    protected $_unification          = -1;

    /**
     * Token offset.
     *
     * @var int
     */
    protected $_offset               = 0;

    /**
     * Constructor.
     *
     * @param   string|int  $name           Name.
     * @param   string  $tokenName      Token name.
     * @param   string|null  $nodeId         Node ID.
     * @param   int     $unification    Unification index.
     * @param   bool    $kept           Whether the token is kept or not in the AST.
     */
    public function __construct(
        $name,
        $tokenName,
        $nodeId,
        $unification,
        $kept = false
    ) {
        parent::__construct($name, null, $nodeId);

        $this->_tokenName   = $tokenName;
        $this->_unification = $unification;
        $this->setKept($kept);

        return;
    }

    /**
     * Get token name.
     *
     * @return string|null
     */
    public function getTokenName()
    {
        return $this->_tokenName;
    }

    /**
     * Set token namespace.
     *
     * @param   string  $namespace    Namespace.
     * @return  string
     */
    public function setNamespace($namespace)
    {
        $old              = $this->_namespace;
        $this->_namespace = $namespace;

        return $old;
    }

    /**
     * Get token namespace.
     *
     * @return  string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Set representation.
     *
     * @param   string  $regex    Representation.
     * @return  string|null
     */
    public function setRepresentation(string $regex)
    {
        $old          = $this->_regex;
        $this->_regex = $regex;

        return $old;
    }

    /**
     * Get token representation.
     *
     * @return  string|null
     */
    public function getRepresentation()
    {
        return $this->_regex;
    }

    /**
     * Get AST of the token representation.
     *
     * @return  \JMS\Serializer\Type\Compiler\Llk\TreeNode
     */
    public function getAST()
    {
        if (null === static::$_regexCompiler) {
            $stream = new File\Read('hoa://Library/Regex/Grammar.pp');
            $stream->rewind();

            static::$_regexCompiler = Compiler\Llk::load($stream);
        }

        if (null === $this->_ast) {
            $this->_ast = static::$_regexCompiler->parse(
                (string) $this->getRepresentation()
            );
        }

        return $this->_ast;
    }

    /**
     * Set token value.
     *
     * @param   string  $value    Value.
     * @return  string
     */
    public function setValue(string $value)
    {
        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    /**
     * Get token value.
     *
     * @return  string
     */
    public function getValue(): string
    {
        return $this->_value;
    }

    /**
     * Set token offset.
     *
     * @param   int  $offset    Offset.
     * @return  int
     */
    public function setOffset($offset)
    {
        $old           = $this->_offset;
        $this->_offset = $offset;

        return $old;
    }

    /**
     * Get token offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * Set whether the token is kept or not in the AST.
     *
     * @param   bool  $kept    Kept.
     * @return  bool
     */
    public function setKept($kept)
    {
        $old         = $this->_kept;
        $this->_kept = $kept;

        return $old;
    }

    /**
     * Check whether the token is kept in the AST or not.
     *
     * @return  bool
     */
    public function isKept()
    {
        return $this->_kept;
    }

    /**
     * Get unification index.
     *
     * @return  int
     */
    public function getUnificationIndex()
    {
        return $this->_unification;
    }
}
