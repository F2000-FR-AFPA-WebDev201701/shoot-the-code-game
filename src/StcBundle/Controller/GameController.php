<?php

namespace StcBundle\Controller;

# Pour les annotations @Template si le nom de la fonction = le nom de la vue,
# sinon @Template(name="@STC/Game/maVue.html.twig

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use StcBundle\Form\ContactType;
use StcBundle\Form\InscriptionType;
use StcBundle\Entity\Game;
use StcBundle\Entity\User;
use StcBundle\Model\Square;

class GameController extends Controller {

    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request) {
        // gestion formulaire de contact
        $oContactForm = $this->createForm(ContactType::class);
        $oContactForm->handleRequest($request);
        if ($oContactForm->isSubmitted() && $oContactForm->isValid()) {
            //dump($oContactForm->getData());
            // prévoir l'envoit d'un email à l'administrateur
        }

        return $this->render('StcBundle:Game:index.html.twig', array(
                    'contactForm' => $oContactForm->createView()));
    }

    /**
     * @Route("/game", name="create_game")
     */
    public function createAction(Request $request) {
        //TO DO
        //Formulaire de création de partie
        // Vérifier si tout ok dans formulaire
        // Création de la partie dans la base
        //On créé une nouvelle partie
        $oGame = new Game();
        $em = $this->getDoctrine()->getManager();
        $em->persist($oGame);
        //On sérialize le plateau pour le stockage en base de données
        $oGame->setBoard(serialize($oGame->getBoard()));
        $em->flush();
        // Redirection vers gameAction avec l'id de la partie
        return $this->redirectToRoute('game', array('id' => $oGame->getId()));
    }

    /**
     * @Route("/game/{id}", name="game")
     */
    public function gameAction(Request $request, $id) {
        //On récupère les informations de la partie demandée
        $rep = $this->getDoctrine()->getRepository('StcBundle:Game');
        $oGame = $rep->find($id);
        //On désérialize les infos du plateau pour récupérer ses cases que l'on pourra lire
        $oGame->setBoard(unserialize($oGame->getBoard()));
        $board = $oGame->getBoard()->getCases();
        //On retourne le tableau de cases
        return $this->render(
            'StcBundle:Game:jouer.html.twig', 
            array(
                'idGame'=> $oGame->getId(),
                'plateau' => $board
        )
        );
    }
    
    /**
    * @Route("/controls/{idGame}/{action}", name="controls")
    */
    public function controlsAction($idGame, $action) {
        
        $rep=$this->getDoctrine()->getRepository('StcBundle:Game');
        $oGame = $rep->find($idGame);
        
        $oGame->setBoard(unserialize($oGame->getBoard()));
        
        $board = $oGame->getBoard();
        
        $board->doAction($action);
        // return $this->render(...);
        
        return $this->render(
            'StcBundle:Game:plateau.html.twig', 
            array(
                    'plateau' => $board
        )
        );
    }

}
