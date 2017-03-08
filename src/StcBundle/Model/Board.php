<?php

namespace StcBundle\Model;

use StcBundle\Model\Square;

class Board {

    private $hauteur = 20;
    private $longueur = 15;
    private $cases = [];

    //Constructeur
    public function __construct() {
        $this->init();
    }

    //Fonctions
    public function init() {
        for ($y = 0; $y < $this->hauteur; $y++) {
            $this->cases[$y] = [];
            for ($x = 0; $x < $this->longueur; $x++) {
                $this->cases[$y][$x] = new Square($y, $x);
            }
        }

        //Tests affichage
        $this->cases[19][7]->setContent('Avion');
        $this->cases[1][2]->setContent('Bloc');
        $this->cases[1][3]->setContent('Bloc');
        $this->cases[1][5]->setContent('Bloc');
        $this->cases[1][6]->setContent('Bloc');
        $this->cases[1][8]->setContent('Bloc');
        $this->cases[1][9]->setContent('Bloc');
        $this->cases[1][11]->setContent('Bloc');
        $this->cases[1][12]->setContent('Bloc');
    }

    //Getters et Setters
    public function getHauteur() {
        return $this->hauteur;
    }

    public function getLongueur() {
        return $this->longueur;
    }

    public function getCases() {
        return $this->cases;
    }

    public function setHauteur($hauteur) {
        $this->hauteur = $hauteur;
    }

    public function setLongueur($longueur) {
        $this->longueur = $longueur;
    }

    public function setCases($cases) {
        $this->cases = $cases;
    }

}
