<?php

namespace StcBundle\Model;

abstract class Movable {

    protected $positionx;
    protected $positiony;

    //Getters et Setters
    public function getPositionx() {
        return $this->positionx;
    }

    public function getPositiony() {
        return $this->positiony;
    }

    public function setPositionx($positionx) {
        $this->positionx = $positionx;
    }

    public function setPositiony($positiony) {
        $this->positiony = $positiony;
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
