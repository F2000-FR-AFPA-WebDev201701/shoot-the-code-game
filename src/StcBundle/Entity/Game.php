<?php

namespace StcBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use StcBundle\Model\Board;

/**
 * @ORM\Entity
 * @ORM\Table(name="Game")
 */
class Game {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id",type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name",type="string", length=100, nullable=false)
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(name="state",type="integer", nullable=false)
     * @Assert\Type(type="integer")
     */
    private $state;

    /**
     * @ORM\Column(name="score",type="integer")
     * @Assert\Type(type="integer")
     */
    private $score;

    /**
     * @ORM\Column(name="maxPlayers",type="integer")
     * @Assert\Type(type="integer")
     */
    private $maxPlayers;

    /**
     * @ORM\Column(name="createdDate",type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    private $createdDate;

    /**
     * @ORM\Column(name="board",type="text")
     */
    private $board;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="games")
     */
    private $users;

    const PENDING_GAME = 0;
    const CURRENT_GAME = 1;
    const END_GAME = 2;

    /**
     * Constructor
     */
    public function __construct($name = 'game', $state = Game::PENDING_GAME, $score = 0, $maxPlayers = 1) {
        $this->name = $name;
        $this->state = $state;
        $this->score = $score;
        $this->maxPlayers = $maxPlayers;
        $this->createdDate = new \Datetime();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        //Création du plateau de jeu
        $this->board = new Board();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Game
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Game
     */
    public function setState($state) {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Set score
     *
     * @param integer $score
     *
     * @return Game
     */
    public function setScore($score) {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore() {
        return $this->score;
    }

    /**
     * Set maxPlayers
     *
     * @param integer $maxPlayers
     *
     * @return Game
     */
    public function setMaxPlayers($maxPlayers) {
        $this->maxPlayers = $maxPlayers;

        return $this;
    }

    /**
     * Get maxPlayers
     *
     * @return integer
     */
    public function getMaxPlayers() {
        return $this->maxPlayers;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Game
     */
    public function setCreatedDate($createdDate) {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate() {
        return $this->createdDate;
    }

    /**
     * Set board
     *
     * @param string $board
     *
     * @return Game
     */
    public function setBoard($board) {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board
     *
     * @return string
     */
    public function getBoard() {
        return $this->board;
    }

    /**
     * Add user
     *
     * @param \StcBundle\Entity\User $user
     *
     * @return Game
     */
    public function addUser(\StcBundle\Entity\User $user) {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \StcBundle\Entity\User $user
     */
    public function removeUser(\StcBundle\Entity\User $user) {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers() {
        return $this->users;
    }

}
