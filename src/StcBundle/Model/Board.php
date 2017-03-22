<?php

namespace StcBundle\Model;

use StcBundle\Model\Square;
use StcBundle\Model\Plane;
use StcBundle\Model\Block;
use StcBundle\Model\Enemy;

class Board {

    // le board : hauteur, longueur et tableau de cases à 2 dimensions
    private $hauteur = 20;
    private $longueur = 15;
    private $cases = [];
    // tableau d'avion vide pour les futurs joueurs
    private $planeTab = [];
    // tableau des blocs couleurs
    private $block = [];
    // Tableau des ennemis
    private $enemy = [];
    // tableau avec la combinaison réponse
    private $combinaison = [];
    // booléen true si la partie est terminée
    private $endGame;
    // variable temps de jeu
    private $chronometer;

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
    public function getEnemy() {
        return $this->enemy;
    }

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

    public function getChronometer() {
        return $this->chronometer;
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

        // Génération d'un nombre variable d'ennemis

        $this->generateEnemies(mt_rand(5, ($this->longueur - 1)));
    }

    public function setChronometer() {
        $this->chronometer = new \DateTime('now');
    }

    public function generateEnemies($nbEnemy) {
        // Génération des ennemis
        for ($i = 0; $i < $nbEnemy; $i++) {
            $oEnemy = new Enemy();
            $x = $oEnemy->getPositionx();
            $y = $oEnemy->getPositiony();

            // Tant que la position actuelle est occupée par un autre ennemi
            while (!$this->cases[$y][$x]->getContent() === null) {
                // On lui attribue une nouvelle position aléatoire
                $oEnemy->setPositionx(mt_rand(0, ($this->longueur - 1)));

                $x = $oEnemy->getPositionx();
                $y = $oEnemy->getPositiony();
            }

            // On assigne le type d'ennemi à la case correspondant à sa position
            $this->cases[$y][$x]->setContent($oEnemy);

            $this->enemy[] = $oEnemy;
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
        // déplacement des ennemis
        $this->moveEnnemies();

        // met à null l'ancienne case du user avion
        $this->cases[$oUserPlane->getPositiony()][$oUserPlane->getPositionx()]->setContent();
        $oUserPlane->move($action);
        // alimente la nouvelle case
        $this->cases[$oUserPlane->getPositiony()][$oUserPlane->getPositionx()]->setContent($oUserPlane);

        switch ($action) {
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

    private function moveEnnemies() {
        // on récupère les ennemis et on met à jour leurs anciennes et nouvelles cases
        $enemys = $this->getEnemy();
        foreach ($enemys as $enemy) {
            // init vars
            $moved = false;
            $dirPossible = ['left', 'right', 'down'];

            while (!$moved) {
                // on choisi une direction aléatoire parmi les possibles restantes
                // on mélange le tableau des positions
                shuffle($dirPossible);
                // on retire et on teste les dernier élément du tableau des positions restantes
                $direction = array_pop($dirPossible);

                if ($direction) {
                    // retourne la nouvelle position pour contrôle : return ['x' => $newPosx, 'y' => $newPosy]
                    $nextPos = $enemy->calculNextPosition($direction);
                    // on vérifie si la case est disponible
                    if ($this->cases[$nextPos['y']][$nextPos['x']]->getContent() === null) {
                        // met à null l'ancienne case de chaque ennemi.
                        $this->cases[$enemy->getPositiony()][$enemy->getPositionx()]->setContent();
                        // met à jour les positions x,y
                        $enemy->move($nextPos['x'], $nextPos['y']);
                        // alimente le contenu de la nouvelle case
                        $this->cases[$enemy->getPositiony()][$enemy->getPositionx()]->setContent($enemy);
                        $moved = true;
                    }
                } else {
                    //si on a testé tous les déplacements, on sort et donc l'enemi reste sur place
                    $moved = true;
                }
            }
        }
    }

}
