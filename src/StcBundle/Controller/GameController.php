<?php

namespace StcBundle\Controller;

# Pour les annotations @Template si le nom de la fonction = le nom de la vue,
# sinon @Template(name="@STC/Game/maVue.html.twig

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use StcBundle\Form\ContactType;
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
//dump($oContactForm->getData());
// prévoir l'envoit d'un email à l'administrateur
        }

        return $this->render('StcBundle:Game:index.html.twig', array(
                    'contactForm' => $oContactForm->createView()
        ));
    }

    /**
     * @Route("/game", name="create_game")
     */
    public function createAction(Request $request) {
        // On vérifie que l'utilisateur est connecté
        if ($request->getSession()->get('userStatus') == 'connected') {
            //On créé une nouvelle partie : nom de la partie, maxPlayer, initialized board.
            $oGame = new Game();
            $oCreateGameForm = $this->createForm(GameType::class, $oGame, array('nom' => $request->getSession()->get('userName')));
            $oCreateGameForm->handleRequest($request);

            //Si le formulaire est envoyé et valide
            if ($oCreateGameForm->isSubmitted() && $oCreateGameForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($oGame);
                $em->flush();

                // Redirection vers gameAction avec l'id de la partie
                return $this->redirectToRoute('join', array('idGame' => $oGame->getId()));
            }
            return $this->render('StcBundle:Game:creategame.html.twig', array(
                        'createGameForm' => $oCreateGameForm->createView())
            );
        } else {
            return $this->redirectToRoute('index');
        }
    }

    /**
     * @Route("/join/{idGame}", name="join")
     */
    public function joinAction(Request $request, $idGame) {
        // On vérifie que l'utilisateur est connecté
        if ($request->getSession()->get('userStatus') == 'connected') {
            $sessionName = $request->getSession()->get('userName');

            // On récupère la partie qui vient d'être crée et passée en paramètre.
            $gameRep = $this->getDoctrine()->getRepository('StcBundle:Game');
            $oGame = $gameRep->find($idGame);

            // recherche de l'id du joueur
            $userRep = $this->getDoctrine()->getRepository('StcBundle:User');
            $oUser = $userRep->findOneBy(array('login' => $sessionName));

            //TODO : Ajouter utilisateur si seulement il n'existe pas déjà dans la partie et que la partie est en attente
            $oGame->addUser($oUser);

            // Si on a atteint le nombre de joueurs max de la partie
            if ($oGame->getMaxPlayers() == count($oGame->getUsers())) {
                $oGame->setState(Game::CURRENT_GAME);

                // set Players
                $oBoard = unserialize($oGame->getBoard());
                $oBoard->setPlayers($oGame->getUsers());

                $oGame->setBoard(serialize($oBoard));
            }

            // on serialize et on met à jour en base
            $this->getDoctrine()->getManager()->flush();

            //debug
//        return $this->render(
//                        'StcBundle:Game:test.html.twig', array(
//                    'game' => $oGame,
//                    'user' => $oUser)
//        );
            // on redirige vers le plateau de jeux
            return $this->redirectToRoute('game', array('id' => $oGame->getId()));
        } else {
            return $this->redirectToRoute('index');
        }
    }

    /**
     * @Route("/game/{id}", name="game")
     */
    public function gameAction(Request $request, $id) {
        // On vérifie que l'utilisateur est connecté
        if ($request->getSession()->get('userStatus') == 'connected') {
            //On récupère les informations de la partie demandée
            $rep = $this->getDoctrine()->getRepository('StcBundle:Game');
            $oGame = $rep->find($id);
            //On désérialize les infos du plateau pour récupérer ses cases que l'on pourra lire
            $oGame->setBoard(unserialize($oGame->getBoard()));
            $board = $oGame->getBoard()->getCases();
            //On retourne le tableau de cases
            return $this->render(
                            'StcBundle:Game:jouer.html.twig', array(
                        'idGame' => $oGame->getId(),
                        'plateau' => $board
                            )
            );
        } else {
            return $this->redirectToRoute('index');
        }
    }

    /**
     * @Route("/list", name="list")
     */
    public function listAction(Request $request) {
        // On vérifie que l'utilisateur est connecté
        if ($request->getSession()->get('userStatus') == 'connected') {
            //On récupères les parties en attente de joueurs
            $rep = $this->getDoctrine()->getRepository('StcBundle:Game');
            $oGame = $rep->findByState(Game::PENDING_GAME);
            return $this->render(
                            'StcBundle:Game:listgame.html.twig', array(
                        'listGame' => $oGame));
        } else {
            return $this->redirectToRoute('index');
        }
    }

    /**
     * @Route("/controls/{idGame}/{action}", name="controls")
     */
    public function controlsAction(Request $request, $idGame, $action) {
        // On vérifie que l'utilisateur est connecté
        if ($request->getSession()->get('userStatus') == 'connected') {

            //On récupère la partie de l'action en cours
            $rep = $this->getDoctrine()->getRepository('StcBundle:Game');
            $oGame = $rep->find($idGame);

            //On récupère les infos du plateau
            $oBoard = unserialize($oGame->getBoard());

            //On effectue l'action demandée suite à l'entrée clavier
            $oBoard->doAction($action);

            //On récupère les nouvelles cases à jour
            $cases = $oBoard->getCases();

            //On recharger le nouveau plateau dans la base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($oGame);
            $oGame->setBoard(serialize($oBoard));
            $em->flush();

            return $this->render(
                            'StcBundle:Game:plateau.html.twig', array(
                        'plateau' => $cases
                            )
            );
        } else {
            return $this->redirectToRoute('index');
        }
    }

}
