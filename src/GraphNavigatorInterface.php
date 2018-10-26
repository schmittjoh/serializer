<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\NotAcceptableException;

interface GraphNavigatorInterface
{
    public const DIRECTION_SERIALIZATION = 1;
    public const DIRECTION_DESERIALIZATION = 2;

    /**
     * Called at the beginning of the serialization process. The navigator should use the traverse the object graph
     * and pass to the $visitor the value of found nodes (following the rules obtained from $context).
     */
    public function initialize(VisitorInterface $visitor, Context $context): void;

    /**
     * Called for each node of the graph that is being traversed.
     *
     * @param mixed $data the data depends on the direction, and type of visitor
     * @param array|null $type array has the format ["name" => string, "params" => array]
     *
     * @return mixed the return value depends on the direction, and type of visitor
     *
     * @throws NotAcceptableException
     */
    public function accept($data, ?array $type = null);
}
