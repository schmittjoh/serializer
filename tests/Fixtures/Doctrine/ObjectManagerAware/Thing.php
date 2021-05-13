<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\ObjectManagerAware;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectManagerAware;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/** @ORM\Entity */
class Thing implements ObjectManagerAware
{
    /**
     * @Serializer\Type("string")
     * @ORM\Id
     * @ORM\Column(type="string", name="ip_address")
     *
     * @var string
     */
    protected $id;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @Serializer\Type("string")
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $name;

    public function injectObjectManager(ObjectManager $objectManager, ClassMetadata $classMetadata)
    {
        $this->objectManager = $objectManager;
        $this->classMetadata = $classMetadata;
    }

    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    public function getClassMetadata(): ClassMetadata
    {
        return $this->classMetadata;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
