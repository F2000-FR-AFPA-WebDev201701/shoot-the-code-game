<?php

namespace StcBundle\Model;

use StcBundle\Model\Square;
use StcBundle\Model\Plane;
use StcBundle\Model\Block;
use StcBundle\Model\Enemy;

class Board {

// le board : hauteur, longueur et tableau de cases Ã  2 dimensions
    const HAUTEUR = 20;
    const LONGUEUR = 15;
    const BONUS_LASER = 1;

    private $cases = [];
// tableau d'avion vide pour les futurs joueurs
    private $planeTab = [];
// tableau des blocs couleurs
    private $block = [];
// Tableau des ennemis
    private $enemies = [];
// tableau avec la combinaison rÃ©ponse
    private $combinaison = [];
// boolÃ©en true si la partie est terminÃ©e
    private $endGame;
// variable temps de jeu
    private $startGameDate;
    //tableau des morts
    private $planeDeaths = [];

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

// GÃ©nÃ©ration des 4 blocs
// GÃ©nÃ©ration de la combinaison
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

    public function getstartGameDate() {
        return $this->startGameDate;
    }

    public function setstartGameDate($date) {
        $this->startGameDate = $date;
    }

    public function isEndGame() {
        return ($this->endGame != null);
    }

    public function setPlaneTab($planeTab) {
        $this->planeTab = $planeTab;
    }

    public function setCases($cases) {
        $this->cases = $cases;
    }

    public function getPlaneDeaths() {
        return $this->planeDeaths;
    }

    public function setPlaneDeaths($planeDeaths) {
        $this->planeDeaths = $planeDeaths;
    }

    public function getTimeGame() {
        if ($this->isEndGame()) {
            //On calcul la différence entre la date de début et de fin de partie
            $time = $this->endGame->diff($this->startGameDate);
            return $time->format('%i:%s');
        }
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

// GÃ©nÃ©ration d'un nombre variable d'ennemis
        $this->generateEnemies(mt_rand(5, (self::LONGUEUR - 1)));
    }

//Fonction gÃ©nÃ©rant des ennemis
    public function generateEnemies($nbEnemy) {
// GÃ©nÃ©ration des ennemis
        for ($i = 0; $i < $nbEnemy; $i++) {
            $oEnemy = new Enemy();
            $x = $oEnemy->getPositionx();
            $y = $oEnemy->getPositiony();

// Tant que la position actuelle est occupÃ©e par un autre ennemi
            while (!$this->cases[$y][$x]->getContent() === null) {
// On lui attribue une nouvelle position alÃ©atoire
                $oEnemy->setPositionx(mt_rand(0, (self::LONGUEUR - 1)));

                $x = $oEnemy->getPositionx();
                $y = $oEnemy->getPositiony();
            }

// On assigne le type d'ennemi Ã  la case correspondant Ã  sa position
            $this->cases[$y][$x]->setContent($oEnemy);

            $this->enemies[] = $oEnemy;
        }
    }

// met Ã  jour le status de chaque bloc couleur et renvoie un boolÃ©en fin de partie true si la combinaison est ok
    public function checkColor() {
        $this->endGame = new \Datetime();
        foreach ($this->block as $key => $oBlock) {
            if ($oBlock->getColor() == $this->combinaison[$key]) {
                $oBlock->setStatus(Block::STATUS_GOOD);
                //On enregistre l'heure de fin de partie
            } elseif (in_array($oBlock->getColor(), $this->combinaison)) {
                $oBlock->setStatus(Block::STATUS_ALMOST);
                $this->endGame = null;
            } else {
                $oBlock->setStatus(Block::STATUS_WRONG);
                $this->endGame = null;
            }
        }
    }

//Fonction supprimant un ennemi
    public function deleteEnemy($enemy) {
        $index = array_search($enemy, $this->enemies);
        if ($index !== false) {
            unset($this->enemies[$index]);
        }
    }

    public function doAction($idUser, $action) {

        $points = 0;
// on rÃ©cupÃ¨re le bon avion, celui du l'utilisateur connectÃ© sur le poste
        $oUserPlane = null;
        foreach ($this->planeTab as $oPlane) {
            if ($oPlane->getIdUser() == $idUser) {
                $oUserPlane = $oPlane;
            }
        }
// Si problÃ¨me (Ã§a ne devrait jamais arriver), au cas oÃ¹ on sort de la fÂ°
        if (!$oUserPlane) {
            return;
        }

        // si on a plus assez d'ennmis Ã  l'Ã©cran on fait respawn
        if (count($this->enemies) <= 3) {
            $this->generateEnemies(mt_rand(5, (self::LONGUEUR - 1)));
        }

//On bouge les diffÃ©rentes entitÃ©s(ennemis, avions)
        $this->moveEnnemies();
//DÃ©placement de l'avion
        $this->movePlane($oUserPlane, $action);
        //On sort de l'action si la partie est terminÃ©e
        if ($this->isEndGame()) {
            return;
        }

        switch ($action) {
            case 'shoot':

                //Tir sur le premier ennemi alignÃ©
                $oTargets = $oUserPlane->shootEnemy($this->enemies);

                if (!$oTargets) {
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
                } else {
                    foreach ($oTargets as $oTarget) {
                        //Si on a trouvÃ© au moins une cible
                        if ($oTarget instanceof Enemy) {
                            //Si la cible est morte
                            if (!$oTarget->isAlive()) {
                                $this->cases[$oTarget->getPositiony()][$oTarget->getPositionx()]->setContent();

                                $points += $oTarget->getPointsEnemy();

                                $this->deleteEnemy($oTarget);
                                if ($oTarget->getBonus()) {
                                    $oUserPlane->addPower($oTarget->getBonus());
                                }
                            }
                        }
                    }
                }
                break;
        }
        return $points;
    }

    public function movePlane($plane, $action) {
//On observe la cable ciblÃ© par l'avion
        $this->calculNextPositionPlane($plane, $action);
    }

//Fonction gÃ©rant le mouvement des ennemis
    public function moveEnnemies() {

// on rÃ©cupÃ¨re les ennemis et on met Ã  jour leurs anciennes et nouvelles cases
        foreach ($this->enemies as $enemy) {
// Pour mettre Ã  jour le dÃ©placement des ennemis,
// on vÃ©rifie qu'on a dÃ©passÃ© le temps minimum autorisÃ©
            $lastMove = $enemy->getLastMoveEnemy();

            $expireMove = clone $lastMove;
            $expireMove = $expireMove->add(new \DateInterval('PT' . $enemy->getVitesseEnemy() . 'S'));

            if ($expireMove < new \DateTime()) {
// Met Ã  jour la position d'un ennemi
                $this->calculNextPositionEnemy($enemy);
            }
        }
    }

//Fonction permettant la mise Ã  jour de la direction aprÃ¨s la validation d'un mouvement ennemi
    public function validDirection($enemy) {
        $direction = $enemy->getDirectionEnemy();
        $x = $enemy->getPositionx();
        $y = $enemy->getPositiony();

//En fonction de l'ennemi on met Ã  jour sa prochaine direction
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

//Fonction permettant de savoir si la cellule cible est vide et comprise dans le plateau
    public function checkisPossible($x, $y) {
        $check = false;
        if ($x > 0 && $x < Board::LONGUEUR && $y >= 2) {
            if ($y < Board::HAUTEUR) {
                $check = ($this->cases[$y][$x]->getContent() == null);
            } elseif ($y == Board::HAUTEUR) {
                $check = true;
            }
        }
        return $check;
    }

//Fonction calculant les dÃ©placements possibles d'un avion
    public function calculNextPositionPlane($plane, $action) {
        $oldPosx = $plane->getPositionx();
        $oldPosy = $plane->getPositiony();
        $newPosx = $oldPosx;
        $newPosy = $oldPosy;

        switch ($action) {
            case 'left':
                if ($oldPosx > 0) {
                    $newPosx = $oldPosx - 1;
                } else {
                    $newPosx = $oldPosx;
                }

                break;

            case 'right':
                if ($oldPosx < Board::LONGUEUR - 1) {
                    $newPosx = $oldPosx + 1;
                } else {
                    $newPosx = $oldPosx;
                }
                break;

            case 'up':
                if ($oldPosy > 2) {
                    $newPosy = $oldPosy - 1;
                } else {
                    $newPosy = $oldPosy;
                }
                break;

            case 'down':
                if ($oldPosy < Board::HAUTEUR - 1) {
                    $newPosy = $oldPosy + 1;
                } else {
                    $newPosy = $oldPosy;
                }
                break;
        }
        if ($this->checkisPossible($newPosx, $newPosy)) {
            // met Ã  null l'ancienne case du user avion
            $this->cases[$plane->getPositiony()][$plane->getPositionx()]->setContent();
            // On dÃ©place l'avion
            $plane->move($newPosx, $newPosy);
            // alimente la nouvelle case
            $this->cases[$plane->getPositiony()][$plane->getPositionx()]->setContent($plane);
        } else {
            $squareTarget = $this->cases[$newPosy][$newPosx]->getContent();
            if ($squareTarget instanceof Enemy) {
                //On dÃ©truit l'ennemi
                $plane->takeDamage($squareTarget->getDamageEnemy());
                if (!$plane->isAlive()) {
                    $this->cases[$plane->getPositiony()][$plane->getPositionx()]->setContent();
                    $this->planeDeaths[] = $plane;
                    unset($this->planeTab[array_search($plane, $this->planeTab)]);
                    if (!$this->checkExistPlayers()) {
                        $this->endGame = new \DateTime;
                    }
                } else {
                    // met Ã  null l'ancienne case du user avion
                    $this->cases[$plane->getPositiony()][$plane->getPositionx()]->setContent();
                    // On dÃ©place l'avion
                    $plane->move($newPosx, $newPosy);
                    // alimente la nouvelle case
                    $this->cases[$plane->getPositiony()][$plane->getPositionx()]->setContent($plane);
                }
                $this->cases[$squareTarget->getPositiony()][$squareTarget->getPositionx()]->setContent();
                unset($this->enemies[array_search($squareTarget, $this->enemies)]);
            }
        }
    }

//Fonction calculant les dÃ©placements possibles d'un ennemi
    public function calculNextPositionEnemy($enemy) {
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
                $newPosy = $oldPosy + 1;
                $newPosx = $oldPosx + 1;
                if ($target instanceof Plane) {
                    $choice = ($enemy->getPositionx() > $target->getPositionx()) ? 'left' : 'right';
                    //On cherche Ã  se rapprocher de l'avion
                    if ($choice == 'left') {
                        $newPosx = $oldPosx - 1;
                    } elseif ($choice == 'right') {
                        $newPosx = $oldPosx + 1;
                    }
                }
                $newPosx = ($newPosx < Board::LONGUEUR && $newPosx >= 0) ? $newPosx : $oldPosx; // gauche ou droite

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
// on choisi une direction alÃ©atoire parmi les possibles restantes
// on mÃ©lange le tableau des positions
                    shuffle($possibilities);
// on retire et on teste les dernier Ã©lÃ©ment du tableau des positions restantes
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
//si on a testÃ© tous les dÃ©placements, on sort et donc l'enemi reste sur place
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

// si l'ennemi essaye de sortir du bas du plateau on le supprime
        if (!array_key_exists($newPosy, $this->cases)) {
// met Ã  null l'ancienne case de chaque ennemi.
            $this->cases[$enemy->getPositiony()][$enemy->getPositionx()]->setContent();
            unset($this->enemies[array_search($enemy, $this->enemies)]);
        }
// sinon on vÃ©rifie si la case est disponible
        else {
            $squareTarget = $this->cases[$newPosy][$newPosx]->getContent();
            if ($squareTarget == null) {
//On met Ã  jour les directions
                $this->validDirection($enemy);
// met Ã  null l'ancienne case de chaque ennemi.
                $this->cases[$enemy->getPositiony()][$enemy->getPositionx()]->setContent();
// met Ã  jour les positions x,y
                $enemy->move($newPosx, $newPosy);
// alimente le contenu de la nouvelle case
                $this->cases[$enemy->getPositiony()][$enemy->getPositionx()]->setContent($enemy);
//On met Ã  jour l'instant du dernier mouvement ennemi
                $enemy->setLastMoveEnemy(new \DateTime());
            } else {
                if ($squareTarget instanceof Plane) {
//L'avion va prendre les dommages
                    $squareTarget->takeDamage($enemy->getDamageEnemy());
                    if (!$squareTarget->isAlive()) {
                        $this->cases[$squareTarget->getPositiony()][$squareTarget->getPositionx()]->setContent();
                        $this->planeDeaths[] = $squareTarget;
                        unset($this->planeTab[array_search($squareTarget, $this->planeTab)]);
                        if (!$this->checkExistPlayers()) {
                            $this->endGame = new \DateTime;
                        }
                    }
                    //On dÃ©truit l'ennemi
                    $this->cases[$enemy->getPositiony()][$enemy->getPositionx()]->setContent();
                    unset($this->enemies[array_search($enemy, $this->enemies)]);
                }
            }
        }
    }

    //VÃ©rifie qu'il y a encore des joueurs dans la partie
    public function checkExistPlayers() {
        return $this->planeTab;
    }

}
