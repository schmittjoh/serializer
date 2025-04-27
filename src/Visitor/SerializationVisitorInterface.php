<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Type\Type;
use JMS\Serializer\VisitorInterface;

/**
 * Interface for visitors.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 * @phpstan-import-type TypeArray from Type
 */
interface SerializationVisitorInterface extends VisitorInterface
{
    /**
     * @param mixed $data
     * @param TypeArray $type
     *
     * @return mixed
     */
    public function visitNull($data, array $type);

    /**
     * @param TypeArray $type
     *
     * @return mixed
     */
    public function visitString(string $data, array $type);

    /**
     * @param TypeArray $type
     *
     * @return mixed
     */
    public function visitBoolean(bool $data, array $type);

    /**
     * @param TypeArray $type
     *
     * @return mixed
     */
    public function visitDouble(float $data, array $type);

    /**
     * @param TypeArray $type
     *
     * @return mixed
     */
    public function visitInteger(int $data, array $type);

    /**
     * @param TypeArray $type
     *
     * @return array|\ArrayObject|void
     */
    public function visitArray(array $data, array $type);

    /**
     * @param TypeArray $type
     * Called before the properties of the object are being visited.
     */
    public function startVisitingObject(ClassMetadata $metadata, object $data, array $type): void;

    /**
     * @param mixed $data
     */
    public function visitProperty(PropertyMetadata $metadata, $data): void;

    /**
     * @param TypeArray $type
     * Called after all properties of the object have been visited.
     *
     * @return array|\ArrayObject|void
     */
    public function endVisitingObject(ClassMetadata $metadata, object $data, array $type);
}
