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
    private $powers = [];

    public function __construct() {
        $this->damagePlane = 1;
        $this->hpPlane = 1;
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

    public function shootEnemy($enemies) {
        $firstTarget = null;
        $aTargets = [];

        //Pour chaque ennemi, on regarde sur la colonne ou on a tiré
        foreach ($enemies as $enemy) {
            //Si c'est la même colonne
            if ($enemy->getPositionx() == $this->positionx) {
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

}
