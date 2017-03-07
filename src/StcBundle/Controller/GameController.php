<?php

namespace StcBundle\Controller;

# Pour les annotations @Template si le nom de la fonction = le nom de la vue,
# sinon @Template(name="@STC/Game/maVue.html.twig

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GameController extends Controller {

    /**
     * @Route("/", name="index")
     */
    public function indexAction() {
        return $this->render('StcBundle:Game:index.html.twig');
    }

}
