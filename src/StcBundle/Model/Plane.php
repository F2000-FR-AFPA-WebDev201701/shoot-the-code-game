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
    //Points de vie de l'avion
    private $hpPlane;

    function __construct() {
        $this->damagePlane = 1;
        $this->hpPlane = 1;
    }

//Getters et Setters
    public function getIdUser() {
        return $this->idUser;
    }

    public function setIdUser($idUser) {
        $this->idUser = $idUser;
    }

    function getDamagePlane() {
        return $this->damagePlane;
    }

    function getHpPlane() {
        return $this->hpPlane;
    }

    function setDamagePlane($damagePlane) {
        $this->damagePlane = $damagePlane;
    }

    function setHpPlane($hpPlane) {
        $this->hpPlane = $hpPlane;
    }

    public function move($x, $y) {
        $this->setPositionx($x);
        $this->setPositiony($y);
    }

    public function shootFirstEnemy($enemys) {
        $target = null;
        //Pour chqua ennemi, on regarde sur la colonne ou on a tiré
        foreach ($enemys as $enemy) {
            //Si c'est la même colonne
            if ($enemy->getPositionx() == $this->positionx) {
                //On assigne l'ennemi comme cible si aucune cible déjà présente
                if ($target == null) {
                    $target = $enemy;
                } else {
                    //Sinon si il y a plusieurs cibles possibles on prend la plus proche
                    if ($enemy->getPositiony() > $target->getPositiony()) {
                        $target = $enemy;
                    }
                }
            }
        }
        if ($target instanceof Enemy) {
            $target->takeDamage($this->damagePlane);
        }

        return $target;
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
