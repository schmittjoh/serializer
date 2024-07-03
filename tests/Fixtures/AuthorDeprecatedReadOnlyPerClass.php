<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\DeprecatedReadOnly;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @deprecated ReadOnly annotation is deprecated
 *
 * @XmlRoot("author")
 * @ReadOnly
 */
#[XmlRoot(name: 'author')]
#[DeprecatedReadOnly]
class AuthorDeprecatedReadOnlyPerClass
{
    /**
     * @ReadOnly
     * @SerializedName("id")
     */
    #[DeprecatedReadOnly]
    #[SerializedName(name: 'id')]
    private $id;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @Type("string")
     * @SerializedName("full_name")
     * @Accessor("getName")
     * @ReadOnly(false)
     */
    #[Type(name: 'string')]
    #[SerializedName(name: 'full_name')]
    #[Accessor(getter: 'getName')]
    #[DeprecatedReadOnly(readOnly: false)]
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}
