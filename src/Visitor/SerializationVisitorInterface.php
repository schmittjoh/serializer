<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\VisitorInterface;

/**
 * Interface for visitors.
 *
 * This contains the minimal set of values that must be supported for any
 * output format.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Asmir Mustafic <goetas@gmail.com>
 */
interface SerializationVisitorInterface extends VisitorInterface
{
    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitNull($data, array $type);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitString(string $data, array $type);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitBoolean(bool $data, array $type);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitDouble(float $data, array $type);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return mixed
     */
    public function visitInteger(int $data, array $type);

    /**
     * @param mixed $data
     * @param array $type
     *
     * @return array|\ArrayObject
     */
    public function visitArray(array $data, array $type);

    /**
     * Called before the properties of the object are being visited.
     *
     * @param mixed $data
     * @param array $type
     */
    public function startVisitingObject(ClassMetadata $metadata, object $data, array $type): void;

    /**
     * @param mixed $data
     */
    public function visitProperty(PropertyMetadata $metadata, $data): void;

    /**
     * Called after all properties of the object have been visited.
     *
     * @param mixed $data
     * @param array $type
     *
     * @return array|\ArrayObject
     */
    public function endVisitingObject(ClassMetadata $metadata, object $data, array $type);
}
