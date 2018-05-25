<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlRoot;

/** @XmlRoot("author") */
class AuthorReadOnly
{
    /**
     * @ReadOnly
     * @SerializedName("id")
     */
    private $id;

    /**
     * @Type("string")
     * @SerializedName("full_name")
     * @Accessor("getName")
     */
    private $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}
