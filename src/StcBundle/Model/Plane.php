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

//Getters et Setters
    public function getIdUser() {
        return $this->idUser;
    }

    public function setIdUser($idUser) {
        $this->idUser = $idUser;
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

}
