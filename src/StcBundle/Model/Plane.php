<?php

namespace StcBundle\Model;

use StcBundle\Model\Movable;

/**
 * Description of Plane
 *
 * @author x
 */
class Plane extends Movable {

// variables du Model Plane
    private $idUser;

//Getters et Setters
    public function getIdUser() {
        return $this->idUser;
    }

    public function setIdUser($idUser) {
        $this->idUser = $idUser;
    }

    function __construct() {

    }

}
