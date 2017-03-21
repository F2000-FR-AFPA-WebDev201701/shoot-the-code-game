<?php

namespace StcBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="Stc_Users")
 */
class User {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id",type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="login",type="string", length=20, nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $login;

    /**
     * @ORM\Column(name="password",type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="Les mots de passe ne sont pas valides.")
     * @Assert\Type("string")
     */
    private $password;

    /**
     * @ORM\Column(name="mail",type="string", length=100, nullable=false)
     * @Assert\Email(
     *     message = "L'email {{ value }} n'est pas valide.",
     *     checkMX = true
     * )
     */
    private $mail;

    /**
     * @ORM\Column(name="lastActionDate",type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    private $lastActionDate;

    /**
     * @ORM\ManyToMany(targetEntity="Game", mappedBy="users")
     */
    private $games;

    /**
     * Constructor
     */
    public function __construct() {
        $this->games = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lastActionDate = new \DateTime();
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
     * Set login
     *
     * @param string $login
     *
     * @return User
     */
    public function setLogin($login) {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;
        //$this->password = sha1($password);

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return User
     */
    public function setMail($mail) {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail() {
        return $this->mail;
    }

    /**
     * Add game
     *
     * @param \StcBundle\Entity\Game $game
     *
     * @return User
     */
    public function addGame(\StcBundle\Entity\Game $game) {
        $game->addUser($this);
        $this->games[] = $game;

        return $this;
    }

    /**
     * Remove game
     *
     * @param \StcBundle\Entity\Game $game
     */
    public function removeGame(\StcBundle\Entity\Game $game) {
        $game->removeUser($this);
        $this->games->removeElement($game);
    }

    /**
     * Get games
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGames() {
        return $this->games;
    }


    /**
     * Set lastActionDate
     *
     * @param \DateTime $lastActionDate
     *
     * @return User
     */
    public function setLastActionDate($lastActionDate)
    {
        $this->lastActionDate = $lastActionDate;

        return $this;
    }

    /**
     * Get lastActionDate
     *
     * @return \DateTime
     */
    public function getLastActionDate()
    {
        return $this->lastActionDate;
    }
}
