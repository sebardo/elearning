<?php
namespace ElearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ElearningBundle\Entity\Course;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="classes")
 * @ORM\Entity(repositoryClass="ElearningBundle\Entity\Repository\ClassesRepository")
 */
class Classes
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @Gedmo\Slug(fields={"name"}, updatable=true, separator="-")
     * @ORM\Column(name="slug", type="string", length=64)
     */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="ElearningBundle\Entity\Course" , inversedBy="classes")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\ManyToMany(targetEntity="CoreBundle\Entity\Actor")
     * @ORM\JoinTable(name="classes_teachers",
     * joinColumns={@ORM\JoinColumn(name="classes_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="teacher_id", referencedColumnName="id")}
     * )
     *
     */
    protected $teachers;

    /**
     * @ORM\ManyToMany(targetEntity="CoreBundle\Entity\Actor")
     * @ORM\JoinTable(name="classes_students",
     * joinColumns={@ORM\JoinColumn(name="classes_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="student_id", referencedColumnName="id")}
     * )
     *
     */
    protected $students;

    
    public function __construct()
    {
        $this->teachers = new ArrayCollection();
        $this->students = new ArrayCollection();
    }

    /**
     * Returns the name
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *                     @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param  string $slug
     * @return Node
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set course
     *
     * @param entity $course
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Get course
     *
     * @return entity
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Add teacher
     *
     * @param  \CoreBundle\Entity\Actor $teacher
     * @return Classes
     */
    public function addTeacher(\CoreBundle\Entity\Actor $teacher)
    {
        $this->teachers[] = $teacher;

        return $this;
    }

    /**
     * Remove teachers
     *
     * @param \CoreBundle\Entity\Actor $teacher
     */
    public function removeTeacher(\CoreBundle\Entity\Actor $teacher)
    {
        $this->teachers->removeElement($teacher);
    }

    /**
     * Get teachers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeachers()
    {
        return $this->teachers;
    }

    /**
     * Add students
     *
     * @param  \CoreBundle\Entity\Actor $student
     * @return Classes
     */
    public function addStudent(\CoreBundle\Entity\Actor $student)
    {
        $this->students[] = $student;

        return $this;
    }

    /**
     * Remove students
     *
     * @param \CoreBundle\Entity\Actor $student
     */
    public function removeStudent(\CoreBundle\Entity\Actor $student)
    {
        $this->students->removeElement($student);
    }

    /**
     * Get students
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStudents()
    {
        return $this->students;
    }
    
    
}
