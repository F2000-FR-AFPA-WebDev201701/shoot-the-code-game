<?php

namespace StcBundle\Model;

use StcBundle\Model\Movable;

class Square extends Movable {
    private $content;
    
    public function getContent() {
        return $this->content;
    }

    public function setContent($content = NULL) {
        $this->content = $content;
    }

}
