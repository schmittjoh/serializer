<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Type\Type;
use JMS\Serializer\VisitorInterface;

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 * @phpstan-import-type TypeArray from Type
 */
interface DeserializationVisitorInterface extends VisitorInterface
{
    /**
     * @param mixed $data
     * @param TypeArray $type
     *
     * @return null
     */
    public function visitNull($data, array $type);

    /**
     * @param mixed $data
     * @param TypeArray $type
     */
    public function visitString($data, array $type): ?string;

    /**
     * @param mixed $data
     * @param TypeArray $type
     */
    public function visitBoolean($data, array $type): ?bool;

    /**
     * @param mixed $data
     * @param TypeArray $type
     */
    public function visitDouble($data, array $type): ?float;

    /**
     * @param mixed $data
     * @param TypeArray $type
     */
    public function visitInteger($data, array $type): ?int;

    /**
     * Returns the class name based on the type of the discriminator map value
     *
     * @param mixed $data
     */
    public function visitDiscriminatorMapProperty($data, ClassMetadata $metadata): string;

    /**
     * @param mixed $data
     * @param TypeArray $type
     *
     * @return array<mixed>
     */
    public function visitArray($data, array $type): array;

    /**
     * Called before the properties of the object are being visited.
     *
     * @param TypeArray $type
     */
    public function startVisitingObject(ClassMetadata $metadata, object $data, array $type): void;

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function visitProperty(PropertyMetadata $metadata, $data);

    /**
     * Called after all properties of the object have been visited.
     *
     * @param mixed $data
     * @param TypeArray $type
     */
    public function endVisitingObject(ClassMetadata $metadata, $data, array $type): object;

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function getResult($data);
}
