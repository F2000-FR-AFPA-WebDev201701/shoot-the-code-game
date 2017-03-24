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

    public function __construct() {
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

    public function move($action = null) {
        $oldPosx = $this->getPositionx();
        $oldPosy = $this->getPositiony();

        switch ($action) {
            case 'left':
                if ($oldPosx > 0) {
                    $newPosx = $oldPosx - 1;
                } else {
                    $newPosx = $oldPosx;
                }
                $this->setPositionx($newPosx);
                break;

            case 'right':
                if ($oldPosx < 14) {
                    $newPosx = $oldPosx + 1;
                } else {
                    $newPosx = $oldPosx;
                }
                $this->setPositionx($newPosx);
                break;

            case 'up':
                if ($oldPosy > 2) {
                    $newPosy = $oldPosy - 1;
                } else {
                    $newPosy = $oldPosy;
                }
                $this->setPositiony($newPosy);
                break;

            case 'down':
                if ($oldPosy < 19) {
                    $newPosy = $oldPosy + 1;
                } else {
                    $newPosy = $oldPosy;
                }
                $this->setPositiony($newPosy);
                break;
        }
    }

    public function shootFirstEnemy($enemys) {
        $target = null;
        //Pour chqua ennemi, on regarde sur la colonne ou on a tiré
        foreach ($enemys as $enemy) {
            //Si c'est la même colonne
            if ($enemy->getPositionx() == $this->positionx) {
                //On assigne l'ennemi comme cible si aucune cible déjà présente
                if ($target === null) {
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

}
