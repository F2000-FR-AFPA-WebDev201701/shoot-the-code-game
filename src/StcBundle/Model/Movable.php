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

    

}
