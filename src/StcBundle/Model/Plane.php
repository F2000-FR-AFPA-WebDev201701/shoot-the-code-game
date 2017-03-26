<?php

namespace StcBundle\Model;

use StcBundle\Model\Movable;

/**
 * Description of Plane
 *
 * @author x
 */
class Plane extends Movable {

// variables du Model Plane
    private $idUser;
    //Attaque de l'avion
    private $damagePlane;
    //Points de vie courant de l'avion
    private $hpPlane;
    //Points de vie max de l'avion
    private $hpMaxPlane;
    private $powers = [];

    public function __construct() {
        $this->damagePlane = 1;
        $this->hpMaxPlane = 4;
        $this->hpPlane = $this->hpMaxPlane;
    }

//Getters et Setters
    public function hasPower($power) {
        return in_array($power, $this->powers);
    }

    public function getPowers() {
        return $this->powers;
    }

    public function addPower($power) {
        $this->powers[] = $power;
    }

    public function getIdUser() {
        return $this->idUser;
    }

    public function setIdUser($idUser) {
        $this->idUser = $idUser;
    }

    public function getDamagePlane() {
        return $this->damagePlane;
    }

    public function getHpPlane() {
        return $this->hpPlane;
    }

    public function setDamagePlane($damagePlane) {
        $this->damagePlane = $damagePlane;
    }

    public function setHpPlane($hpPlane) {
        $this->hpPlane = $hpPlane;
    }

    public function getHpMaxPlane() {
        return $this->hpMaxPlane;
    }

    public function setHpMaxPlane($hpMaxPlane) {
        $this->hpMaxPlane = $hpMaxPlane;
    }

    public function move($x, $y) {
        $this->setPositionx($x);
        $this->setPositiony($y);
    }

    public function shootEnemy($enemies) {
        $firstTarget = null;
        $aTargets = [];

        //Pour chaque ennemi, on regarde sur la colonne ou on a tiré
        foreach ($enemies as $enemy) {
            //Si c'est la même colonne
            if ($enemy->getPositionx() == $this->positionx && $enemy->getPositiony() > $this->positiony) {
                $aTargets[] = $enemy;

                //On assigne l'ennemi comme cible si aucune cible déjà présente
                //Sinon si il y a plusieurs cibles possibles on prend la plus proche
                if ($firstTarget == null) {
                    $firstTarget = $enemy;
                } elseif ($enemy->getPositiony() > $firstTarget->getPositiony()) {
                    $firstTarget = $enemy;
                }
            }
        }

        if ($aTargets) {
            if ($this->hasPower(Board::BONUS_LASER)) {
                foreach ($aTargets as $target) {
                    $target->takeDamage($this->damagePlane);
                }
            } else {
                $firstTarget->takeDamage($this->damagePlane);
                $aTargets = [$firstTarget];
            }
        }
        return $aTargets;
    }

    public function takeDamage($damage) {
        //On calcul les points de vie apres attaque
        $newhp = $this->hpPlane - $damage;
        //Si les points de vie sont inférieur à 0 on les remet à 0 pour éviter des problèmes
        $this->hpPlane = ($newhp > 0) ? $newhp : 0;
    }

    public function isAlive() {
        return $this->hpPlane > 0;
    }

}
