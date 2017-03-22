<?php

namespace StcBundle\Model;

use StcBundle\Model\Movable;

/**
 * Description of Enemy
 *
 * @author x
 */
class Enemy extends Movable {

// variables du Model Enemy
    private $idEnemy;
    private $typeEnemy;

    const TYPES = ['html', 'css', 'js', 'php', 'sql'];
    const MOVES = ['left', 'right', 'down'];

    public function __construct() {
        $type = ['html', 'css', 'js', 'php', 'sql'];
        $randomPosx = mt_rand(0, 14);
        $this->setPositionx($randomPosx);
        $this->setPositiony(2);
        //$this->typeEnemy = self::TYPES[mt_rand(0, count(self::TYPES)-1)];
        $this->typeEnemy = 'php';
    }

//Getters et Setters
    public function getIdEnemy() {
        return $this->idEnemy;
    }

    public function setIdEnemy($idEnemy) {
        $this->idEnemy = $idEnemy;
    }

    public function getTypeEnemy() {
        return $this->typeEnemy;
    }

    public function setTypeEnemy($typeEnemy) {
        $this->typeEnemy = $typeEnemy;
    }

    public function calculNextPosition() {
        $direction = self::MOVES[mt_rand(0, count(self::MOVES) - 1)];
        $oldPosx = $this->getPositionx();
        $oldPosy = $this->getPositiony();
        switch ($direction) {
            case 'left':
                if ($oldPosx > 0) {
                    $newPosx = $oldPosx - 1;
                } else {
                    $newPosx = $oldPosx;
                }
                $newPosy = $oldPosy;
                break;
            case 'right':
                if ($oldPosx < 14) {
                    $newPosx = $oldPosx + 1;
                } else {
                    $newPosx = $oldPosx;
                }
                $newPosy = $oldPosy;
                break;
            case 'down':
                if ($oldPosy < 19) {
                    $newPosy = $oldPosy + 1;
                } else {
                    $newPosy = $oldPosy;
                }
                $newPosx = $oldPosx;
                break;
        }
        return [
            'x' => $newPosx,
            'y' => $newPosy,
        ];
    }

    public function move($posx, $posy) {
        $this->setPositionx($posx);
        $this->setPositiony($posy);
    }

}
