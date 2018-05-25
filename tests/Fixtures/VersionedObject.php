<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Until;

class VersionedObject
{
    /**
     * @Until("1.0.0")
     */
    private $name;

    /**
     * @Since("1.0.1")
     * @SerializedName("name")
     */
    private $name2;

    public function __construct($name, $name2)
    {
        $this->name = $name;
        $this->name2 = $name2;
    }
}
