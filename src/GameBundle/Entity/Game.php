<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="GameBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="games")
     */
    private $user;

    /**
     * @var guid
     *
     * @ORM\Column(name="hash", type="guid", unique=true)
     */
    private $hash;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="mode", type="integer")
     */
    private $mode;

    /**
     * @var bool
     *
     * @ORM\Column(name="finished", type="boolean")
     */
    private $finished = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="abandoned", type="boolean")
     */
    private $abandoned = false;

    /**
     * @var string
     *
     * @ORM\Column(name="gameplay", type="string", length=255)
     */
    private $gameplay;
    
    /**
     * @var string
     *
     * @ORM\Column(name="winner", type="string", nullable=true)
     */
    private $winner;
    
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
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set user
     *
     * @param User $user
     *
     * @return Game
     */
    public function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }

    /**
     * Set hash
     *
     * @param guid $hash
     *
     * @return Game
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return guid
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Game
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Game
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set mode
     *
     * @param integer $mode
     *
     * @return Game
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get mode
     *
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set finished
     *
     * @param boolean $finished
     *
     * @return Game
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get finished
     *
     * @return bool
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * Set abandoned
     *
     * @param boolean $abandoned
     *
     * @return Game
     */
    public function setAbandoned($abandoned)
    {
        $this->abandoned = $abandoned;

        return $this;
    }

    /**
     * Get abandoned
     *
     * @return bool
     */
    public function getAbandoned()
    {
        return $this->abandoned;
    }

    /**
     * Set gameplay
     *
     * @param string $gameplay
     *
     * @return Game
     */
    public function setGamePlay($gameplay)
    {
        $this->gameplay = $gameplay;

        return $this;
    }

    /**
     * Get gameplay
     *
     * @return string
     */
    public function getGamePlay()
    {
        return $this->gameplay;
    }

    /**
     * Set winner
     *
     * @param string $winner
     *
     * @return Game
     */
    public function setWinner($winner)
    {
        $this->winner = $winner;

        return $this;
    }

    /**
     * Get winner
     *
     * @return string
     */
    public function getWinner()
    {
        return $this->winner;
    }
}

