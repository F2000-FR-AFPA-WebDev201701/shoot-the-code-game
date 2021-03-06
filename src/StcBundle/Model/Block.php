<?php

namespace StcBundle\Model;

use StcBundle\Model\Movable;

class Block extends Movable {

    const STATUS_WRONG = 1;
    const STATUS_ALMOST = 2;
    const STATUS_GOOD = 3;

// Initialisation à la couleur 0 (grise)
    private $color = 0;
    private $status = self::STATUS_WRONG;

    public function getId() {
        return $this->id;
    }

// Passe à la couleur suivante et se réinitialise après 8
    public function nextColor() {
        if ($this->status === self::STATUS_GOOD) {
            return $this;
        }

        $this->color++;
        if ($this->color == 9) {
            $this->color = 1;
        }

        return $this;
    }

    public function getColor() {
        return $this->color;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

}
