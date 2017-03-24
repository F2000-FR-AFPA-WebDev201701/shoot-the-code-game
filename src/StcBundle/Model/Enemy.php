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

    const TYPES = ['html', 'css', 'js', 'php', 'sql'];
    const MOVES = ['left', 'right', 'down'];

    public function __construct() {
        $this->lastMoveEnemy = new \Datetime();
        $this->vitesseEnemy = 2;
        $this->damageEnemy = 1;
        $this->pointsEnemy = 1;
        $this->hpEnemy = 1;
        $this->setPositionx(mt_rand(0, 14));
        $this->setPositiony(2);
        $this->typeEnemy = self::TYPES[mt_rand(0, count(self::TYPES) - 1)];
        //On initialise les ennemis sql à aller à droite à leur création
        if ($this->typeEnemy == 'sql') {
            $this->directionEnemy = 'right';
        }
        //On initialise les ennemis css à aller à descendre à leur création
        if ($this->typeEnemy == 'css') {
            $this->directionEnemy = 'down';
        }
    }

//Getters et Setters
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

    public function takeDamage($damage) {
        //On calcul les points de vie apres attaque
        $newhp = $this->hpEnemy - $damage;
        //Si les points de vie sont inférieur à 0 on les remet à 0 pour éviter des problèmes
        $this->hpEnemy = ($newhp > 0) ? $newhp : 0;
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

    function getDirectionEnemy() {
        return $this->directionEnemy;
    }

    function setDirectionEnemy($directionEnemy) {
        $this->directionEnemy = $directionEnemy;
    }

}
