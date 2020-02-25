<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\NonCastableTypeException;
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
     */
    protected function assertValueCanBeCastToString($value)
    {
        if (is_array($value)) {
            throw new NonStringCastableTypeException($value);
        }

        if (is_object($value) && method_exists($value, '__toString') === false) {
            throw new NonStringCastableTypeException($value);
        }
    }

    /**
     * logic according to intval https://www.php.net/manual/en/function.intval.php
     * "intval() should not be used on objects, as doing so will emit an E_NOTICE level error and return 1."
     */
    protected function assertValueCanBeCastToInt($value)
    {
        if (is_object($value) && !$value instanceof \SimpleXMLElement) {
            throw new NonIntCastableTypeException($value);
        }
    }

    /**
     *  logic according to floatval https://www.php.net/manual/en/function.floatval.php
     * "floatval() should not be used on objects, as doing so will emit an E_NOTICE level error and return 1."
     */
    protected function assertValueCanCastToFloat($value)
    {
        if (is_object($value) && !$value instanceof \SimpleXMLElement) {
            throw new NonFloatCastableTypeException($value);
        }
    }
}
