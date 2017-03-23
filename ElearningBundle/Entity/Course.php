<?php
namespace ElearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CoreBundle\Entity\Site;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="course")
 * @ORM\Entity(repositoryClass="ElearningBundle\Entity\Repository\CourseRepository")
 */
class Course
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
     * @ORM\ManyToOne(targetEntity="\CoreExtraBundle\Entity\Site", inversedBy="courses")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $site;

    /**
     * @ORM\OneToMany(targetEntity="ElearningBundle\Entity\Test", mappedBy="course")
     */
    protected $tests;

    public function __construct()
    {
        $this->tests = new ArrayCollection();
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
     * Set site
     *
     * @param  Site $site
     * @return Post
     */
    public function setSite(Site $site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return Post
     */
    public function getSite()
    {
        return $this->site;
    }
    
    /**
     * Add classes
     *
     * @param  \ElearningBundle\Entity\Test $test
     * @return Course
     */
    public function addTest(\ElearningBundle\Entity\Test $test)
    {
        $this->tests[] = $test;

        return $this;
    }

    /**
     * Remove tests
     *
     * @param \ElearningBundle\Entity\Test $test
     */
    public function removeTest(\ElearningBundle\Entity\Test $test)
    {
        $this->tests->removeElement($test);
    }

    /**
     * Get classes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTests()
    {
        return $this->tests;
    }
}
