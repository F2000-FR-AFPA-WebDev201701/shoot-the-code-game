<?php

namespace StcBundle\Model;

class Square {

    private $positionx;
    private $positiony;
    private $content;

    //Constructeur avec des paramêtres
    public function __construct($positionx = 0, $positiony = 0, $content = '') {
        $this->positionx = $positionx;
        $this->positiony = $positiony;
        $this->content = $content;
    }

    //Getters et Setters
    public function getPositionx() {
        return $this->positionx;
    }

    public function getPositiony() {
        return $this->positiony;
    }

    public function getContent() {
        return $this->content;
    }

    public function setPositionx($positionx) {
        $this->positionx = $positionx;
    }

    public function setPositiony($positiony) {
        $this->positiony = $positiony;
    }

    public function setContent($content) {
        $this->content = $content;
    }

}
