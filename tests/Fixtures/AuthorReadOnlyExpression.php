<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlRoot;

/** @XmlRoot("author") */
class AuthorReadOnlyExpression
{
    /**
     * @ReadOnly(if="1 == 1")
     * @SerializedName("id")
     */
    private $id;

    /**
     * @ReadOnly(if="1 == 2")
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
