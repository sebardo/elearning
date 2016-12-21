<?php
namespace ElearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ElearningBundle\Entity\Test;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="ask")
 * @ORM\Entity(repositoryClass="ElearningBundle\Entity\Repository\AskRepository")
 */
class Ask
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
     * @ORM\ManyToOne(targetEntity="ElearningBundle\Entity\Test" , inversedBy="asks")
     * @ORM\JoinColumn(name="test_id", referencedColumnName="id")
     */
    protected $test;

    /**
     * @ORM\OneToMany(targetEntity="ElearningBundle\Entity\Answer", mappedBy="ask", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $answers;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="_order", type="integer", nullable=true)
     */
    private $order;

    
    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    /**
     * Returns the name
     * @return string
     */
    public function __toString()
    {
        return $this->getSlug();
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
     * Set test
     *
     * @param entity $test
     */
    public function setTest(Test $test)
    {
        $this->test = $test;
    }

    /**
     * Get test
     *
     * @return entity
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Add answer
     *
     * @param  \ElearningBundle\Entity\Answer $answer
     * @return Test
     */
    public function addAnswer(\ElearningBundle\Entity\Answer $answer)
    {
        $this->answers[] = $answer;

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \ElearningBundle\Entity\Test $answer
     */
    public function removeAnswer(\ElearningBundle\Entity\Answer $answer)
    {
        $this->answers->removeElement($answer);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnswers()
    {
        return $this->answers;
    }
    
    /**
     * Set order
     *
     * @param integer $order
     *
     * @return Slider
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }
    
}
