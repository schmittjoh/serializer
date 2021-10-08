<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\PersistendCollection;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/** @ORM\Entity */
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
    #[Serializer\Type(name: 'string')]
    protected $id;

    /**
     * @Serializer\Type("string")
     * @ORM\Column(type="string")
     *
     * @var string
     */
    #[Serializer\Type(name: 'string')]
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="SmartPhone")
     *
     * @var SmartPhone
     */
    #[Serializer\Type(name: SmartPhone::class)]
    private $smartPhone;

    /**
     * @param string $id
     * @param string $name
     * @param string $smartPhone
     */
    public function __construct(
        $id,
        $name,
        $smartPhone
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
