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

    public function __construct() {
        $type = ['html', 'css', 'js', 'php', 'sql'];
        $randomPosx = mt_rand(0, 14);
        $this->setPositionx($randomPosx);
        $this->setPositiony(2);
        //$this->typeEnemy = $type[mt_rand(0, 4)];
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

    public function move() {
        $newPosy = $this->getPositiony() + 1;
        $this->setPositiony($newPosy);
    }

}
