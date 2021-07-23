<?php


namespace JMS\Serializer\Tests\Fixtures\TypedProperties;


class UnionTypedProperties
{
    private string|int $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}