<?php
namespace ElearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use CoreBundle\Entity\Actor;
use ElearningBundle\Entity\Test;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="ElearningBundle\Entity\Repository\ActorTestRepository")
 * @ORM\Table(name="actor_test")
 */
class ActorTest 
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="evaluate_date", type="datetime")
     */
    private $evaluateDate;

    /**
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Actor" )
     * @ORM\JoinColumn(name="actor_id", referencedColumnName="id")
     */
    protected $actor;
    
    /**
     * @ORM\ManyToOne(targetEntity="ElearningBundle\Entity\Test" , inversedBy="actorTests")
     * @ORM\JoinColumn(name="test_id", referencedColumnName="id")
     */
    protected $test;
    
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $data;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set evaluateDate
     *
     * @param string $evaluateDate
     *
     * @return ActorLog
     */
    public function setEvaluateDate($evaluateDate)
    {
        $this->evaluateDate = $evaluateDate;

        return $this;
    }

    /**
     * Get evaluateDate
     *
     * @return string
     */
    public function getEvaluateDate()
    {
        return $this->evaluateDate;
    }

    /**
     * Set actor
     *
     * @param entity $actor
     */
    public function setActor(Actor $actor)
    {
        $this->actor = $actor;
    }

    /**
     * Get actor
     *
     * @return entity
     */
    public function getActor()
    {
        return $this->actor;
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
     * Set data
     *
     * @param entity $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get data
     *
     * @return entity
     */
    public function getData()
    {
        return $this->data;
    }
    
}
