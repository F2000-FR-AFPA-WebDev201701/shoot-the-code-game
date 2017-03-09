<?php

namespace StcBundle\Controller;

# Pour les annotations @Template si le nom de la fonction = le nom de la vue,
# sinon @Template(name="@STC/Game/maVue.html.twig

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use StcBundle\Form\ContactType;
use StcBundle\Form\InscriptionType;
use StcBundle\Form\GameType;
use StcBundle\Entity\Game;

class GameController extends Controller {

    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request) {
        // gestion formulaire de contact
        $oContactForm = $this->createForm(ContactType::class);
        $oContactForm->handleRequest($request);
        if ($oContactForm->isSubmitted() && $oContactForm->isValid()) {
            // dump($oContactForm->getData());
            // prévoir l'envoit d'un email à l'administrateur
        }

        // gestion formulaire d'inscription
        $oInscriptionForm = $this->createForm(InscriptionType::class);
        $oInscriptionForm->handleRequest($request);
        if ($oInscriptionForm->isSubmitted() && $oInscriptionForm->isValid()) {
            // dump($oInscriptionForm->getData());
            // prévoir l'inscription en base de l'utilisateur
        }

        $oGame = new Game();
        $oCreateGameForm = $this->createForm(GameType::class, $oGame);
        $oCreateGameForm->handleRequest($request);

        // gestion formulaire de connexion
//        $oUserForm = $this->createForm(UserType::class);
//        $oUserForm->handleRequest($request);
//        if ($oUserForm->isSubmitted() && $oUserForm->isValid()) {
//            // dump($oUserForm->getData());
//            // prévoir la connexion de l'utilisateur
//        }

        return $this->render('StcBundle:Game:index.html.twig', array(
                    'inscriptionForm' => $oInscriptionForm->createView(),
                    'contactForm' => $oContactForm->createView(),
        ));
    }

    /**
     * @Route("/game", name="create_game")
     */
    public function createAction(Request $request) {
        //On créé une nouvelle partie
        $oGame = new Game();
        $oCreateGameForm = $this->createForm(GameType::class, $oGame, array('nom' => $request->getSession()->get('userName')));
        $oCreateGameForm->handleRequest($request);

        //Si le formulaire est envoyé et valide
        if ($oCreateGameForm->isSubmitted() && $oCreateGameForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($oGame);
            //On sérialize le plateau pour le stockage en base de données
            $oGame->setBoard(serialize($oGame->getBoard()));
            $em->flush();
            // Redirection vers gameAction avec l'id de la partie
            return $this->redirectToRoute('game', array('id' => $oGame->getId()));
        }
        return $this->render('StcBundle:Game:creategame.html.twig', array(
                    'createGameForm' => $oCreateGameForm->createView()));
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
        return $this->render('StcBundle:Game:jouer.html.twig', array(
                    'plateau' => $board));
    }

}
