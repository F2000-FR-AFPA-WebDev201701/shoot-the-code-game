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
        for ($x = 0; $x < $this->hauteur; $x++) {
            $this->cases[$x] = [];
            for ($y = 0; $y < $this->longueur; $y++) {
                $this->cases[$x][$y] = new Square($y, $x);
            }
        }

        //Tests affichage
        $this->cases[1][2]->setContent('couleur1');
        $this->cases[1][5]->setContent('couleur4');
        $this->cases[1][9]->setContent('couleur5');
        $this->cases[1][12]->setContent('couleur7');
        $this->cases[18][7]->setContent('avion');
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
    
    public function doAction($action) {
        switch ($action) {
            case 'left':
                
                break;
            case 'right':
                
                break;
            case 'up':
                
                break;
            case 'down':
                
                break;
            case 'shoot':
                
                break;
        }
    }

}
