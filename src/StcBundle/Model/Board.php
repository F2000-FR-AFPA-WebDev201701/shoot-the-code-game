<?php

namespace StcBundle\Model;

use StcBundle\Model\Square;
use StcBundle\Model\Plane;

class Board {

    private $hauteur = 20;
    private $longueur = 15;
    private $cases = [];
// tableau d'avion vide pour les futurs joueurs
    private $planeTab = [];

//Constructeur
    public function __construct() {
        $this->init();
    }

//Fonctions
    public function init() {
        for ($y = 0; $y < $this->hauteur; $y++) {
            $this->cases[$y] = [];
            for ($x = 0; $x < $this->longueur; $x++) {
                $this->cases[$y][$x] = new Square($x, $y);
            }
        }

//Tests affichage
        $this->cases[1][2]->setContent('couleur1');
        $this->cases[1][5]->setContent('couleur4');
        $this->cases[1][9]->setContent('couleur5');
        $this->cases[1][12]->setContent('couleur7');
    }

//Getters et Setters
    public function getPlaneTab() {
        return $this->planeTab;
    }

    public function getHauteur() {
        return $this->hauteur;
    }

    public function getLongueur() {
        return $this->longueur;
    }

    public function getCases() {
        return $this->cases;
    }

    public function setPlaneTab($planeTab) {
        $this->planeTab = $planeTab;
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

    public function setPlayers($oUserTab) {
        $startX = 5;
        foreach ($oUserTab as $oUser) {
            // P1 case x=7, P2 case x=9
            $startX = $startX + 2;
            $oAvion = new Plane();
            $oAvion->setPositionx($startX);
            $oAvion->setPositiony(18);
            $oAvion->setUserId($oUser->getId());
            $this->planeTab[] = $oAvion;
            $this->cases[$oAvion->getPositiony()][$oAvion->getPositionx()]->setContent('avion');
        }
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
