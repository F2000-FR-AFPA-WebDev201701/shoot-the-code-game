<?php

namespace StcBundle\Model;

use StcBundle\Model\Square;
use StcBundle\Model\Plane;
use StcBundle\Model\Block;
use StcBundle\Model\Enemy;

class Board {

    // le board : hauteur, longueur et tableau de cases à 2 dimensions
    const HAUTEUR = 20;
    const LONGUEUR = 15;

    private $cases = [];
    // tableau d'avion vide pour les futurs joueurs
    private $planeTab = [];
    // tableau des blocs couleurs
    private $block = [];
    // Tableau des ennemis
    private $enemies = [];
    // tableau avec la combinaison réponse
    private $combinaison = [];
    // booléen true si la partie est terminée
    private $endGame;
    // variable temps de jeu
    private $gameDate;

    //Constructeur
    public function __construct() {
        $this->init();
    }

    //Fonctions
    public function init() {
        for ($y = 0; $y < self::HAUTEUR; $y++) {
            $this->cases[$y] = [];
            for ($x = 0; $x < self::LONGUEUR; $x++) {
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
    public function getEnemies() {
        return $this->enemies;
    }

    public function getPlaneTab() {
        return $this->planeTab;
    }

    public function getCases() {
        return $this->cases;
    }

    public function getCombinaison() {
        return $this->combinaison;
    }

    public function getGameDate() {
        return $this->gameDate;
    }

    public function setGameDate($date) {
        $this->gameDate = $date;
    }

    public function isEndGame() {
        return $this->endGame;
    }

    public function setPlaneTab($planeTab) {
        $this->planeTab = $planeTab;
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
        $this->generateEnemies(mt_rand(5, (self::LONGUEUR - 1)));
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
                $oEnemy->setPositionx(mt_rand(0, (self::LONGUEUR - 1)));

                $x = $oEnemy->getPositionx();
                $y = $oEnemy->getPositiony();
            }

            // On assigne le type d'ennemi à la case correspondant à sa position
            $this->cases[$y][$x]->setContent($oEnemy);

            $this->enemies[] = $oEnemy;
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

    public function deleteEnemy($enemy) {
        $index = array_search($enemy, $this->enemies);
        if ($index !== false) {
            unset($this->enemies[$index]);
        }
    }

    public function doAction($idUser, $action) {

        $points = 0;
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

//        // si on a plus assez d'ennmis à l'écran on fait respawn
//        if (count($this->enemies) <= 3) {
//            $this->generateEnemies(mt_rand(5, (self::LONGUEUR - 1)));
//        }
        // déplacement des ennemis
        $this->moveEnnemies();
        // met à null l'ancienne case du user avion
        $this->cases[$oUserPlane->getPositiony()][$oUserPlane->getPositionx()]->setContent();
        $oUserPlane->move($action);
        // alimente la nouvelle case
        $this->cases[$oUserPlane->getPositiony()][$oUserPlane->getPositionx()]->setContent($oUserPlane);
        switch ($action) {
            case 'shoot':
                //Tir sur le premier ennemi aligné
                $oTarget = $oUserPlane->shootFirstEnemy($this->enemies);
                //Si on a trouvé une cible
                if ($oTarget instanceof Enemy) {
                    //Si la cible est morte
                    if (!$oTarget->isAlive()) {
                        $this->cases[$oTarget->getPositiony()][$oTarget->getPositionx()]->setContent();
                        $points = $oTarget->getPointsEnemy();
                        $this->deleteEnemy($oTarget);
                    }
                } else {
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
                }
        }
        return $points;
    }

    private function moveEnnemies() {

        // on récupère les ennemis et on met à jour leurs anciennes et nouvelles cases
        foreach ($this->enemies as $enemy) {
            // Pour mettre à jour le déplacement des ennemis,
            // on vérifie qu'on a dépassé le temps minimum autorisé
            $lastMove = $enemy->getLastMoveEnemy();

            $expireMove = clone $lastMove;
            $expireMove = $expireMove->add(new \DateInterval('PT' . $enemy->getVitesseEnemy() . 'S'));

            if ($expireMove < new \DateTime()) {
                // Met à jour la position d'un ennemi
                $this->calculNextPosition($enemy);
            }
        }
    }

    public function validDirection($enemy) {
        $direction = $enemy->getDirectionEnemy();
        $x = $enemy->getPositionx();
        $y = $enemy->getPositiony();

        //En fonction de l'ennemi on met à jour sa prochaine direction
        switch ($enemy->getTypeEnemy()) {
            case 'css':
                if ($direction == 'down') {
                    if ($y >= 2) {
                        $enemy->setDirectionEnemy('up');
                    }
                } elseif ($direction == 'up') {
                    if ($y >= 3) {
                        $enemy->setDirectionEnemy('down');
                    }
                }
                break;

            case 'sql':
                if ($direction == 'left') {
                    if ($x == 0) {
                        $enemy->setDirectionEnemy('right');
                    }
                } elseif ($direction == 'right') {
                    if ($x == Board::LONGUEUR - 1) {
                        $enemy->setDirectionEnemy('left');
                    }
                }
                break;

            default:
                break;
        }
    }

    public function checkisPossible($x, $y) {
        $check = false;
        if ($x > 0 && $x < Board::LONGUEUR && $y >= 2) {
            $check = ($this->cases[$y][$x]->getContent() == null);
        }
        return $check;
    }

    public function calculNextPosition($enemy) {
        $oldPosx = $enemy->getPositionx();
        $oldPosy = $enemy->getPositiony();
        $direction = $enemy->getDirectionEnemy();

        switch ($enemy->getTypeEnemy()) {
            case 'html':
                $target = null;
                //On recherche l'avion le plus proche
                foreach ($this->planeTab as $oPlane) {
                    if ($target == null) {
                        $target = $oPlane;
                        $distance = abs($enemy->getPositionx() - $oPlane->getPositionx());
                    } else {
                        $newDistance = abs($enemy->getPositionx() - $oPlane->getPositionx());
                        if ($newDistance < $distance) {
                            $distance = $newDistance;
                            $target = $oPlane;
                        }
                    }
                }
                $choice = ($enemy->getPositionx() > $target->getPositionx()) ? 'left' : 'right';
                //On cherche à se rapprocher de l'avion
                if ($choice == 'left') {
                    $direction = $oldPosx - 1;
                } elseif ($choice == 'right') {
                    $direction = $oldPosx + 1;
                }
                $newPosx = ($direction < Board::LONGUEUR ) ? $direction : $oldPosx; // gauche ou droite
                $newPosy = $oldPosy + 1;
                break;
            case 'css':
                $newPosy = $oldPosy;
                $newPosx = $oldPosx;
                if ($direction == 'down') {
                    if ($oldPosy >= 2) {
                        $newPosy += 2;
                    }
                } elseif ($direction == 'up') {
                    if ($oldPosy >= 3) {
                        $newPosy -= 1;
                    }
                }
                break;
            case 'js':
                $newPosy = $oldPosy;
                $newPosx = $oldPosx;
                $possibilities = ['left', 'right', 'down'];

                $moved = false;
                while (!$moved) {
                    // on choisi une direction aléatoire parmi les possibles restantes
                    // on mélange le tableau des positions
                    shuffle($possibilities);
                    // on retire et on teste les dernier élément du tableau des positions restantes
                    $choice = array_pop($possibilities);

                    if ($choice) {
                        switch ($choice) {
                            case 'left':
                                $newPosx2 = ($oldPosx > 0) ? $oldPosx - 1 : $oldPosx;
                                $newPosy2 = $oldPosy;
                                break;
                            case 'right':
                                $newPosx2 = ($oldPosx < Board::LONGUEUR - 1) ? $oldPosx + 1 : $oldPosx;
                                $newPosy2 = $oldPosy;
                                break;
                            case 'down':
                                $newPosy2 = $oldPosy + 1;
                                $newPosx2 = $oldPosx;
                                break;
                        }
                        if ($moved = $this->checkIsPossible($newPosx2, $newPosy2)) {
                            $newPosy = $newPosy2;
                            $newPosx = $newPosx2;
                        }
                        break;
                    } else {
                        //si on a testé tous les déplacements, on sort et donc l'enemi reste sur place
                        $moved = true;
                    }
                }
                break;

            case 'php':
                $newPosx = $oldPosx;
                $newPosy = $oldPosy + 1;
                break;
            case 'sql':
                $newPosy = $oldPosy;
                $newPosx = $oldPosx;
                if ($direction == 'left') {
                    if ($oldPosx > 0) {
                        $newPosx -= 1;
                    } else {
                        $newPosy += 1;
                    }
                } elseif ($direction == 'right') {
                    if ($oldPosx != Board::LONGUEUR - 1) {
                        $newPosx += 1;
                    } else {
                        $newPosy += 1;
                    }
                }
                break;

            default:
                break;
        }

        // init vars
        $moved = false;

        while (!$moved) {

            //if ($direction) {
            // si l'ennemi essaye de sortir du bas du plateau on le supprime
            if (!array_key_exists($newPosy, $this->cases)) {
                // met à null l'ancienne case de chaque ennemi.
                $this->cases[$enemy->getPositiony()][$enemy->getPositionx()]->setContent();
                unset($this->enemies[array_search($enemy, $this->enemies)]);
                $moved = true;
            }
            // sinon on vérifie si la case est disponible
            elseif (($this->cases[$newPosy][$newPosx]->getContent() == null)) {
                //On met à jour les directions
                $this->validDirection($enemy);
                // met à null l'ancienne case de chaque ennemi.
                $this->cases[$enemy->getPositiony()][$enemy->getPositionx()]->setContent();
                // met à jour les positions x,y
                $enemy->move($newPosx, $newPosy);
                // alimente le contenu de la nouvelle case
                $this->cases[$enemy->getPositiony()][$enemy->getPositionx()]->setContent($enemy);
                //On met à jour l'instant du dernier mouvement ennemi
                $enemy->setLastMoveEnemy(new \DateTime());
                $moved = true;
            } else {
                //si on a testé tous les déplacements, on sort et donc l'ennemi reste sur place
                $moved = true;
            }
        }
    }

}
