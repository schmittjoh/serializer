<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
#[ORM\Entity]
class Clazz extends AbstractModel
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /** @ORM\ManyToOne(targetEntity = "JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Teacher") */
    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    private $teacher;

    /** @ORM\ManyToMany(targetEntity = "JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Student") */
    #[ORM\ManyToMany(targetEntity: Student::class)]
    private $students;

    public function __construct(Teacher $teacher, array $students)
    {
        $this->teacher = $teacher;
        $this->students = new ArrayCollection($students);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTeacher()
    {
        return $this->teacher;
    }

    public function getStudents()
    {
        return $this->students;
    }
}
