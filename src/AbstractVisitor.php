<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\NonFloatCastableTypeException;
use JMS\Serializer\Exception\NonIntCastableTypeException;
use JMS\Serializer\Exception\NonStringCastableTypeException;

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

    /**
     * {@inheritdoc}
     */
    public function prepare($data)
    {
        return $data;
    }

    protected function getElementType(array $typeArray): ?array
    {
        if (false === isset($typeArray['params'][0])) {
            return null;
        }

        if (isset($typeArray['params'][1]) && \is_array($typeArray['params'][1])) {
            return $typeArray['params'][1];
        } else {
            return $typeArray['params'][0];
        }
    }

    /**
     * logic according to strval https://www.php.net/manual/en/function.strval.php
     * "You cannot use strval() on arrays or on objects that do not implement the __toString() method."
     *
     * @param mixed $value
     */
    protected function assertValueCanBeCastToString($value): void
    {
        if (is_array($value)) {
            throw new NonStringCastableTypeException($value);
        }

        if (is_object($value) && !method_exists($value, '__toString')) {
            throw new NonStringCastableTypeException($value);
        }
    }

    /**
     * logic according to intval https://www.php.net/manual/en/function.intval.php
     * "intval() should not be used on objects, as doing so will emit an E_NOTICE level error and return 1."
     *
     * @param mixed $value
     */
    protected function assertValueCanBeCastToInt($value): void
    {
        if (is_object($value) && !$value instanceof \SimpleXMLElement) {
            throw new NonIntCastableTypeException($value);
        }
    }

    /**
     *  logic according to floatval https://www.php.net/manual/en/function.floatval.php
     * "floatval() should not be used on objects, as doing so will emit an E_NOTICE level error and return 1."
     *
     * @param mixed $value
     */
    protected function assertValueCanCastToFloat($value): void
    {
        if (is_object($value) && !$value instanceof \SimpleXMLElement) {
            throw new NonFloatCastableTypeException($value);
        }
    }

    protected function mapRoundMode(?string $roundMode = null): int
    {
        switch ($roundMode) {
            case 'HALF_DOWN':
                $roundMode = PHP_ROUND_HALF_DOWN;
                break;
            case 'HALF_EVEN':
                $roundMode = PHP_ROUND_HALF_EVEN;
                break;
            case 'HALF_ODD':
                $roundMode = PHP_ROUND_HALF_ODD;
                break;
            case 'HALF_UP':
            default:
                $roundMode = PHP_ROUND_HALF_UP;
        }

        return $roundMode;
    }
}
