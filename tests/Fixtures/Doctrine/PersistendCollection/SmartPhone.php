<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\PersistendCollection;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/** @ORM\Entity */
#[ORM\Entity]
class SmartPhone
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
     * @Serializer\Type("ArrayCollection<JMS\Serializer\Tests\Fixtures\Doctrine\PersistendCollection\App>")
     * @Serializer\SerializedName("applications")
     * @ORM\OneToMany (targetEntity="App", mappedBy="smartPhone", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection<int, App>
     */
    #[Serializer\SerializedName(name: 'applications')]
    #[Serializer\Type(name: 'ArrayCollection<JMS\Serializer\Tests\Fixtures\Doctrine\PersistendCollection\App>')]
    #[ORM\OneToMany(targetEntity: App::class, mappedBy: 'smartPhone', cascade: ['persist'], orphanRemoval: true)]
    private $apps;

    /**
     * @param string $name
     * @param string $phoneId
     */
    public function __construct($name, $phoneId)
    {
        $this->name = $name;
        $this->id = $phoneId;
        $this->apps = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addApp(App $app): void
    {
        $this->apps[] = $app;
    }

    /**
     * @param Criteria|null $criteria
     *
     * @return Collection<int, App>
     */
    public function getApps(?Criteria $criteria = null): Collection
    {
        if (null === $criteria) {
            $criteria = Criteria::create();
        }

        return $this->apps->matching($criteria);
    }

    public function getAppsRaw(): Collection
    {
        return $this->apps;
    }
}
