<?php

namespace StcBundle\Model;

use Doctrine\ORM\Mapping as ORM;

class Block
{
    // Initialisation à la couleur 0 (grise)
    private $color = 0;

    public function getId()
    {
        return $this->id;
    }
    
    // Passe à la couleur suivante et se réinitialise après 8
    public function nextColor()
    {
        $this->color = $this->color + 1;
        
        if ($this->color == 9) {
            $this->color = 1;
        }

        return $this;
    }

    public function getColor()
    {
        return $this->color;
    }
}

