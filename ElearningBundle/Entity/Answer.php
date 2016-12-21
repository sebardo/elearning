<?php
namespace ElearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ElearningBundle\Entity\Ask;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="answer")
 * @ORM\Entity(repositoryClass="ElearningBundle\Entity\Repository\AnswerRepository")
 */
class Answer
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
     * @ORM\ManyToOne(targetEntity="ElearningBundle\Entity\Ask" , inversedBy="answers")
     * @ORM\JoinColumn(name="ask_id", referencedColumnName="id")
     */
    protected $ask;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $correct=false;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="_order", type="integer", nullable=true)
     */
    private $order;
    
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
     * Set ask
     *
     * @param entity $ask
     */
    public function setAsk(Ask $ask)
    {
        $this->ask = $ask;
    }

    /**
     * Get ask
     *
     * @return entity
     */
    public function getAsk()
    {
        return $this->ask;
    }

    /**
     * Set correct
     *
     * @param boolean $correct
     */
    public function setCorrect($correct)
    {
        $this->correct = $correct;

        return $this;
    }

    /**
     * Get correct
     *
     * @return boolean
     */
    public function isCorrect()
    {
        return $this->correct;
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
