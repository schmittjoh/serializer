<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\PersistendCollection;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/** @ORM\Entity */
#[ORM\Entity]
class App
{
    /**
     * @Serializer\SerializedName("id")
     * @Serializer\Type("string")
     * @ORM\Id
     * @ORM\Column(type="string", name="id")
     *
     * @var string
     */
    #[Serializer\SerializedName(name: 'id')]
    #[Serializer\Type(name: 'string')]
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, name: 'id')]
    protected $id;

    /**
     * @Serializer\Type("string")
     * @ORM\Column(type="string")
     *
     * @var string
     */
    #[Serializer\Type(name: 'string')]
    #[ORM\Column(type: Types::STRING)]
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="SmartPhone")
     * @Serializer\Type("JMS\Serializer\Tests\Fixtures\Doctrine\PersistendCollection\SmartPhone")
     *
     * @var SmartPhone
     */
    #[ORM\ManyToOne(targetEntity: SmartPhone::class)]
    #[Serializer\Type(name: SmartPhone::class)]
    private $smartPhone;

    public function __construct(
        string $id,
        string $name,
        SmartPhone $smartPhone
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->smartPhone = $smartPhone;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSmartPhone(): SmartPhone
    {
        return $this->smartPhone;
    }
}
