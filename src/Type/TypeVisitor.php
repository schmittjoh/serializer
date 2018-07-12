<?php

declare(strict_types=1);

namespace JMS\Serializer\Type;

use Hoa\Compiler\Llk\TreeNode;
use Hoa\Visitor\Element;
use Hoa\Visitor\Visit;
use JMS\Serializer\Type\Exception\InvalidNode;
use function strpos;

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
        }

        throw new InvalidNode();
    }

    /**
     * @return string|mixed[]
     */
    private function visitSimpleType(TreeNode $element)
    {
        $tokenNode = $element->getChild(0);
        $token = $tokenNode->getValueToken();
        $value = $tokenNode->getValueValue();

        if ('name' === $token) {
            return ['name' => $value, 'params' => []];
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
}
