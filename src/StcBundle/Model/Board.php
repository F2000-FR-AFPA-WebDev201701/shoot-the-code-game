<?php

namespace StcBundle\Model;

use StcBundle\Model\Square;
use StcBundle\Model\Plane;
use StcBundle\Model\Block;

class Board {

    // le board : hauteur, longueur et tableau de cases à 2 dimensions
    private $hauteur = 20;
    private $longueur = 15;
    private $cases = [];
    // tableau d'avion vide pour les futurs joueurs
    private $planeTab = [];
    // tableau des blocs couleurs
    private $block = [];
    // tableau avec la combinaison réponse
    private $combinaison = [];
    // booléen true si la partie est terminée
    private $endGame;

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

        // Génération des 4 blocs
        // Génération de la combinaison
        for ($i = 0; $i < 4; $i++) {
            $this->block[] = new Block();
            $this->combinaison[] = mt_rand(1, 8);
        }
        // Initialisation de la couleur
        $this->cases[1][2]->setContent($this->block[0]);
        $this->cases[1][5]->setContent($this->block[1]);
        $this->cases[1][9]->setContent($this->block[2]);
        $this->cases[1][12]->setContent($this->block[3]);
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

    public function getCombinaison() {
        return $this->combinaison;
    }

    public function isEndGame() {
        return $this->endGame;
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
        $startX = 4;
        foreach ($oUserTab as $oUser) {
            // P1 case x=6, P2 case x=8
            $startX = $startX + 2;
            $oAvion = new Plane();
            $oAvion->setPositionx($startX);
            $oAvion->setPositiony(18);
            $oAvion->setIdUser($oUser->getId());
            $this->planeTab[] = $oAvion;
            $this->cases[$oAvion->getPositiony()][$oAvion->getPositionx()]->setContent($oAvion);
        }
    }

    // met à jour le status de chaque bloc couleur et renvoie un booléen fin de partie true si la combinaison est ok
    public function checkColor() {
        $this->endGame = true;
        foreach ($this->block as $key => $oBlock) {
            if ($oBlock->getColor() == $this->combinaison[$key]) {
                $oBlock->setStatus(Block::STATUS_GOOD);
            } elseif (in_array($oBlock->getColor(), $this->combinaison)) {
                $oBlock->setStatus(Block::STATUS_ALMOST);
                $this->endGame = false;
            } else {
                $oBlock->setStatus(Block::STATUS_WRONG);
                $this->endGame = false;
            }
        }
    }

    public function doAction($idUser, $action) {
        // on récupère le bon avion, celui du l'utilisateur connecté sur le poste
        $oUserPlane = null;
        foreach ($this->planeTab as $oPlane) {
            if ($oPlane->getIdUser() == $idUser) {
                $oUserPlane = $oPlane;
            }
        }

        // Si problème (ça ne devrait jamais arriver), au cas où on sort de la f°
        if (!$oUserPlane) {
            return;
        }

        switch ($action) {
            case 'left':
                $oldPosx = $oUserPlane->getPositionx();
                $oldPosy = $oUserPlane->getPositiony();
                if ($oldPosx > 0) {
                    $newPosx = $oldPosx - 1;
                } else {
                    $newPosx = $oldPosx;
                }
                $oUserPlane->setPositionx($newPosx);
                $this->cases[$oldPosy][$oldPosx]->setContent();
                $this->cases[$oldPosy][$newPosx]->setContent($oUserPlane);
                break;

            case 'right':
                $oldPosx = $oUserPlane->getPositionx();
                $oldPosy = $oUserPlane->getPositiony();
                if ($oldPosx < 14) {
                    $newPosx = $oldPosx + 1;
                } else {
                    $newPosx = $oldPosx;
                }
                $oUserPlane->setPositionx($newPosx);
                $this->cases[$oldPosy][$oldPosx]->setContent();
                $this->cases[$oldPosy][$newPosx]->setContent($oUserPlane);
                break;

            case 'up':
                $oldPosx = $oUserPlane->getPositionx();
                $oldPosy = $oUserPlane->getPositiony();
                if ($oldPosy > 2) {
                    $newPosy = $oldPosy - 1;
                } else {
                    $newPosy = $oldPosy;
                }
                $oUserPlane->setPositiony($newPosy);
                $this->cases[$oldPosy][$oldPosx]->setContent();
                $this->cases[$newPosy][$oldPosx]->setContent($oUserPlane);
                break;

            case 'down':
                $oldPosx = $oUserPlane->getPositionx();
                $oldPosy = $oUserPlane->getPositiony();
                if ($oldPosy < 19) {
                    $newPosy = $oldPosy + 1;
                } else {
                    $newPosy = $oldPosy;
                }
                $oUserPlane->setPositiony($newPosy);
                $this->cases[$oldPosy][$oldPosx]->setContent();
                $this->cases[$newPosy][$oldPosx]->setContent($oUserPlane);
                break;

            case 'shoot':
                switch ($oUserPlane->getPositionx()) {
                    case 2:
                        $this->block[0]->nextColor();
                        break;
                    case 5:
                        $this->block[1]->nextColor();
                        break;
                    case 9:
                        $this->block[2]->nextColor();
                        break;
                    case 12:
                        $this->block[3]->nextColor();
                        break;
                }
                $this->checkColor();
                break;
        }
    }

}
