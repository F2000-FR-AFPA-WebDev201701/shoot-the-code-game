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
    
    public function __construct()
    {
        $this->setPositiony(mt_rand(0,15));
        $this->setPositionx(2);
        $this->typeEnemy = self::TYPES[mt_rand(0, count(self::TYPES)-1)];
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

    public function setTypeEnemy($idEnemy) {
        $this->typeEnemy = $typeEnemy;
    }
    
    public function move()
    {
        $newPositionx = $this->getPositionx() + 1;
        $this->setPositionx($newPositionx);
    }

}
