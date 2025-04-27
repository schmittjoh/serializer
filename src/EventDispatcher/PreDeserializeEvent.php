<?php

declare(strict_types=1);

namespace JMS\Serializer\EventDispatcher;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Type\Type;

/**
 * @phpstan-import-type TypeArray from Type
 */
class PreDeserializeEvent extends Event
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @param mixed $data
     * @param TypeArray $type
     */
    public function __construct(DeserializationContext $context, $data, array $type)
    {
        parent::__construct($context, $type);

        $this->data = $data;
    }

    public function setType(string $name, array $params = []): void
    {
        $this->type = ['name' => $name, 'params' => $params];
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}
