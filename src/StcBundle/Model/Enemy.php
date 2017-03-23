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
    // l'ennemi porte éventuellement un bonus
    private $bonus;

    const TYPES = ['html', 'css', 'js', 'php', 'sql'];
    const MOVES = ['left', 'right', 'down'];
    const BONUS = [Board::BONUS_LASER];

    public function __construct() {

        $this->lastMoveEnemy = new \Datetime();
        $this->vitesseEnemy = 7;
        $this->damageEnemy = 1;
        $this->pointsEnemy = 1;
        $this->hpEnemy = 1;
        //$randomPosx = mt_rand(0, 14);
        //$this->setPositionx($randomPosx);
        //$this->setPositiony(2);
        $randomPosy = mt_rand(0, 14);
        $this->setPositiony($randomPosy);
        $this->setPositionx(8);
        $this->typeEnemy = self::TYPES[mt_rand(0, count(self::TYPES) - 1)];
        $bonusLoot = mt_rand(0, 9);
        //if ($bonusLoot == 5) {
        $this->bonus = self::BONUS[mt_rand(0, count(self::BONUS) - 1)];
        //}
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

    function getDamageEnemy() {
        return $this->damageEnemy;
    }

    function getHpEnemy() {
        return $this->hpEnemy;
    }

    function setDamageEnemy($damageEnemy) {
        $this->damageEnemy = $damageEnemy;
    }

    function setHpEnemy($hpEnemy) {
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

    function getPointsEnemy() {
        return $this->pointsEnemy;
    }

    function setPointsEnemy($pointsEnemy) {
        $this->pointsEnemy = $pointsEnemy;
    }

}
