<?php

declare(strict_types=1);

namespace JMS\Serializer\Type;

use Hoa\Compiler\Llk\TreeNode;
use Hoa\Visitor\Element;
use Hoa\Visitor\Visit;
use JMS\Serializer\Type\Exception\InvalidNode;
use function strpos;

@trigger_error(sprintf('Class "%s" is deprecated and will be removed in the next major version, use %s instead.', TypeVisitor::class, ''), E_USER_DEPRECATED);

/**
 * @deprecated This class is no longer in use and will be removed in the next major version,
 *             @see https://github.com/schmittjoh/serializer/issues/1182
 */
final class TypeVisitor implements Visit
{
    /**
     * {@inheritdoc}
     */
    public function visit(Element $element, &$handle = null, $eldnah = null)
    {
        switch ($element->getId()) {
            case '#simple_type':
                return $this->visitSimpleType($element);
            case '#compound_type':
                return $this->visitCompoundType($element, $handle, $eldnah);
            case '#array':
                return $this->visitArrayType($element, $handle, $eldnah);
        }

        throw new InvalidNode();
    }

    /**
     * @return string|mixed[]
     */
    private function visitSimpleType(TreeNode $element)
    {
        $tokenNode = $element->getChild(0);

        if (!$tokenNode->isToken()) {
            return $tokenNode->accept($this);
        }

        $token = $tokenNode->getValueToken();
        $value = $tokenNode->getValueValue();

        if ('name' === $token) {
            return ['name' => $value, 'params' => []];
        }

        if ('empty_string' === $token) {
            return '';
        }

        if ('null' === $token) {
            return null;
        }

        if ('number' === $token) {
            return false === strpos($value, '.') ? intval($value) : floatval($value);
        }

        $escapeChar = 'quoted_string' === $token ? '"' : "'";

        if (false === strpos($value, $escapeChar)) {
            return $value;
        }

        return str_replace($escapeChar . $escapeChar, $escapeChar, $value);
    }

    private function visitCompoundType(TreeNode $element, ?int &$handle, ?int $eldnah): array
    {
        $nameToken = $element->getChild(0);
        $parameters = array_slice($element->getChildren(), 1);

        return [
            'name' => $nameToken->getValueValue(),
            'params' => array_map(
                function (TreeNode $node) use ($handle, $eldnah) {
                    return $node->accept($this, $handle, $eldnah);
                },
                $parameters
            ),
        ];
    }

    private function visitArrayType(TreeNode $node, ?int &$handle, ?int $eldnah): array
    {
        return array_map(
            function (TreeNode $child) {
                return $child->accept($this);
            },
            $node->getChildren()
        );
    }
}
