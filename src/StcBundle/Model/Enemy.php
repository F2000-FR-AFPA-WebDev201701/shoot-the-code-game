<?php

namespace StcBundle\Model;

use StcBundle\Model\Movable;

/**
 * Description of Enemy
 *
 * @author x
 */
class Enemy extends Movable {

// variables du Model Enemy
    private $idEnemy;
    //Type d'ennemi
    private $typeEnemy;
    //Vitesse de l'ennemi
    private $vitesseEnemy;
    //Date de dernière action de l'ennemi
    private $lastMoveEnemy;
    //Attaque de l'ennemi
    private $damageEnemy;
    //Points de vie de l'ennemi
    private $hpEnemy;
    //Points donnés par l'ennemi
    private $pointsEnemy;
    //Direction pour le sql
    private $directionEnemy;
    // l'ennemi porte éventuellement un bonus
    private $bonus;

    const TYPES = ['html', 'css', 'js', 'php', 'sql'];
    const MOVES = ['left', 'right', 'down'];
    const BONUS = [Board::BONUS_LASER];

    public function __construct() {
        $this->lastMoveEnemy = new \Datetime();
        $this->vitesseEnemy = 1;
        $this->damageEnemy = 1;
        $this->pointsEnemy = 1;
        $this->hpEnemy = 1;
        $this->setPositionx(mt_rand(0, 14));
        $this->setPositiony(2);
        $this->typeEnemy = self::TYPES[mt_rand(0, count(self::TYPES) - 1)];

        $bonusLoot = mt_rand(0, 9);
        if ($bonusLoot == 5) {
            $this->bonus = self::BONUS[mt_rand(0, count(self::BONUS) - 1)];
        }

        switch ($this->typeEnemy) {
            case 'html':
                $this->damageEnemy = 2;
                $this->hpEnemy = 2;
                break;
            case 'css':
                $this->damageEnemy = 3;
                $this->hpEnemy = 2;
                //On initialise les ennemis css à aller à descendre à leur création
                $this->directionEnemy = 'down';
                break;
            case 'sql':
                $this->damageEnemy = 3;
                $this->hpEnemy = 2;
                //On initialise les ennemis sql à aller à droite à leur création
                $this->directionEnemy = 'right';
                break;
            case 'php':
                $this->damageEnemy = 1;
                $this->hpEnemy = 4;
                break;
            case 'js':
                $this->damageEnemy = 2;
                $this->hpEnemy = 1;
                break;

            default:
                break;
        }
    }

//Getters et Setters
    public function getBonus() {
        return $this->bonus;
    }

    public function getIdEnemy() {
        return $this->idEnemy;
    }

    public function setIdEnemy($idEnemy) {
        $this->idEnemy = $idEnemy;
    }

    public function getTypeEnemy() {
        return $this->typeEnemy;
    }

    public function setTypeEnemy($typeEnemy) {
        $this->typeEnemy = $typeEnemy;
    }

    public function getDamageEnemy() {
        return $this->damageEnemy;
    }

    public function getHpEnemy() {
        return $this->hpEnemy;
    }

    public function setDamageEnemy($damageEnemy) {
        $this->damageEnemy = $damageEnemy;
    }

    public function setHpEnemy($hpEnemy) {
        $this->hpEnemy = $hpEnemy;
    }

    public function calculNextPosition($direction) {
        $oldPosx = $this->getPositionx();
        $oldPosy = $this->getPositiony();

        switch ($direction) {
            case 'left':
                if ($oldPosx > 0) {
                    $newPosx = $oldPosx - 1;
                } else {
                    $newPosx = $oldPosx;
                }
                $newPosy = $oldPosy;
                break;
            case 'right':
                if ($oldPosx < Board::LONGUEUR - 1) {
                    $newPosx = $oldPosx + 1;
                } else {
                    $newPosx = $oldPosx;
                }
                $newPosy = $oldPosy;
                break;
            case 'down':
                $newPosy = $oldPosy + 1;
                $newPosx = $oldPosx;
                break;
        }
        return [
            'x' => $newPosx,
            'y' => $newPosy,
        ];
    }

    /**
     * On calcul les points de vie apres attaque
     * @param type $damage
     */
    public function takeDamage($damage) {
        $this->hpEnemy -= $damage;
    }

    public function isAlive() {
        return $this->hpEnemy > 0;
    }

    public function move($posx, $posy) {
        $this->setPositionx($posx);
        $this->setPositiony($posy);
        //On met à jour l'instant du dernier mouvement ennemi
        $this->lastMoveEnemy = new \DateTime();
    }

    public function getVitesseEnemy() {
        return $this->vitesseEnemy;
    }

    public function setVitesseEnemy($vitesseEnemy) {
        $this->vitesseEnemy = $vitesseEnemy;
    }

    public function getLastMoveEnemy() {
        return $this->lastMoveEnemy;
    }

    public function setLastMoveEnemy($lastMoveEnemy) {
        $this->lastMoveEnemy = $lastMoveEnemy;
    }

    public function getPointsEnemy() {
        return $this->pointsEnemy;
    }

    public function setPointsEnemy($pointsEnemy) {
        $this->pointsEnemy = $pointsEnemy;
    }

    function getDirectionEnemy() {
        return $this->directionEnemy;
    }

    function setDirectionEnemy($directionEnemy) {
        $this->directionEnemy = $directionEnemy;
    }

}
