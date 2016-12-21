<?php
namespace ElearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ElearningBundle\Entity\Course;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="test")
 * @ORM\Entity(repositoryClass="ElearningBundle\Entity\Repository\TestRepository")
 */
class Test
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
     * @ORM\ManyToOne(targetEntity="ElearningBundle\Entity\Course" , inversedBy="tests")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * @ORM\OneToMany(targetEntity="ElearningBundle\Entity\Ask", mappedBy="test")
     */
    protected $asks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active=false;
    
    /**
     * @ORM\OneToMany(targetEntity="ElearningBundle\Entity\ActorTest", mappedBy="actor", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $actorTests;
    
    public function __construct()
    {
        $this->asks = new ArrayCollection();
        $this->actorTests = new ArrayCollection();
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
     * Add ask
     *
     * @param  \ElearningBundle\Entity\Ask $ask
     * @return Test
     */
    public function addAsk(\ElearningBundle\Entity\Ask $ask)
    {
        $this->asks[] = $ask;

        return $this;
    }

    /**
     * Remove asks
     *
     * @param \ElearningBundle\Entity\Test $ask
     */
    public function removeTest(\ElearningBundle\Entity\Ask $ask)
    {
        $this->asks->removeElement($ask);
    }

    /**
     * Get asks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAsks()
    {
        return $this->asks;
    }
    
    /**
     * Is active?
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Product
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }
    
    
    /**
     * Add actorTest
     *
     * @param  \ElearningBundle\Entity\ActorTest $actorTest
     * @return Actor
     */
    public function addActorTest(\ElearningBundle\Entity\ActorTest $actorTest)
    {
        $this->actorTests[] = $actorTest;

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \ElearningBundle\Entity\ActorTest $actorTest
     */
    public function removeActorTest(\ElearningBundle\Entity\ActorTest $actorTest)
    {
        $this->actorTests->removeElement($actorTest);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActorTests()
    {
        return $this->actorTests;
    }
}
