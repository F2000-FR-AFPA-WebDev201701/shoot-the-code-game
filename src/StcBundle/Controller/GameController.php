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
// TODO : prévoir l'envoit d'un email à l'administrateur
        }

        return $this->render('StcBundle:Game:index.html.twig', array(
                    'contactForm' => $oContactForm->createView()
        ));
    }

    /**
     * @Route("/game", name="create_game")
     */
    public function createAction(Request $request) {
        $rendu = $this->redirectToRoute('index');
        // On vérifie que l'utilisateur est connecté
        if ($request->getSession()->get('userStatus') == 'connected') {
            //On créé une nouvelle partie : nom de la partie, maxPlayer, initialized board.
            $oGame = new Game();
            $oCreateGameForm = $this->createForm(GameType::class, $oGame, array('nom' => $request->getSession()->get('userName')));
            $oCreateGameForm->handleRequest($request);
            $rendu = $this->render('StcBundle:Game:creategame.html.twig', array(
                'createGameForm' => $oCreateGameForm->createView())
            );
            //Si le formulaire est envoyé et valide
            if ($oCreateGameForm->isSubmitted() && $oCreateGameForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($oGame);
                $em->flush();

                // Redirection vers gameAction avec l'id de la partie
                $rendu = $this->redirectToRoute('join', array('idGame' => $oGame->getId()));
            }
//            return $this->render('StcBundle:Game:creategame.html.twig', array(
//                        'createGameForm' => $oCreateGameForm->createView())
//            );
            $this->updateAction($request);
        }
        return $rendu;
    }

    /**
     * @Route("/join/{idGame}", name="join")
     */
    public function joinAction(Request $request, $idGame) {
        $rendu = $this->redirectToRoute('index');
        // On vérifie que l'utilisateur est connecté
        if ($request->getSession()->get('userStatus') == 'connected') {

            // On récupère la partie qui vient d'être crée et passée en paramètre.
            $gameRep = $this->getDoctrine()->getRepository('StcBundle:Game');
            $oGame = $gameRep->find($idGame);

            // recherche de l'id du joueur
            $userRep = $this->getDoctrine()->getRepository('StcBundle:User');
            $oUser = $userRep->find($request->getSession()->get('userId'));

            // Ajouter un utilisateur si seulement il n'existe pas déjà dans la partie et que la partie est en attente
            // Partie en attente signifie nombre de joueurs max non atteint
            if ($oGame->getState() == Game::PENDING_GAME && !($oGame->getUsers()->contains($oUser))) {
                $oGame->addUser($oUser);
            }
            // Si on a atteint le nombre de joueurs max de la partie
            if ($oGame->getMaxPlayers() == count($oGame->getUsers())) {
                $oGame->setState(Game::CURRENT_GAME);

                // On met à jour les Players
                $oBoard = unserialize($oGame->getBoard());
                $oBoard->setPlayers($oGame->getUsers());

                //on met à jour le board
                $oGame->setBoard(serialize($oBoard));
            }

            // on serialize et on met à jour en base
            $this->getDoctrine()->getManager()->flush();

            // on redirige vers le plateau de jeux
            $rendu = $this->redirectToRoute('game', array('id' => $oGame->getId()));
            $this->updateAction($request, $idGame);
        }

        return $rendu;
    }

    /**
     * @Route("/game/{id}", name="game")
     */
    public function gameAction(Request $request, $id) {
        $rendu = $this->redirectToRoute('index');
        // On vérifie que l'utilisateur est autorisé à effectuer l'action
        if ($request->getSession()->get('userStatus') == 'connected') {
            //On récupère les informations de la partie demandée
            $rep = $this->getDoctrine()->getRepository('StcBundle:Game');
            $oGame = $rep->find($id);
            //si la partie est toujours en attente
            if ($oGame->getState() == Game::PENDING_GAME) {
                $rendu = $this->render('StcBundle:Game:jouer.html.twig', array(
                    'game' => $oGame));
            } elseif (($oGame->getState() == Game::CURRENT_GAME)) {
                //On désérialize les infos du plateau pour récupérer ses cases que l'on pourra lire
                $oGame->setBoard(unserialize($oGame->getBoard()));
                $board = $oGame->getBoard()->getCases();
                //On retourne le tableau de cases
                $rendu = $this->render(
                        'StcBundle:Game:jouer.html.twig', array(
                    'game' => $oGame,
                    'plateau' => $board
                        )
                );
            }
            $this->updateAction($request, $id);
        }
        return $rendu;
    }

    /**
     * @Route("/list/{page}", name="list")
     */
    public function listAction(Request $request, $page) {
        $rendu = $this->redirectToRoute('index');
        // On vérifie que l'utilisateur est connecté
        if ($request->getSession()->get('userStatus') == 'connected') {
            //On récupères les parties en attente de joueurs
            $rep = $this->getDoctrine()->getRepository('StcBundle:Game');

            //On fixe le nombre de parties à afficher dans l'affichage des parties à rejoindre
            $nbParties = 5;
            //On compte le nombre de parties en attente
            $nbPendingGame = count($rep->findByState(Game::PENDING_GAME));
            //On calcul le nombre de pages à retourner
            $nbPages = floor($nbPendingGame / $nbParties);
            //On ajoute une page si on est compris dans le nombre de page à afficher
            if ($nbPendingGame % 5 != 0) {
                $nbPages += 1;
            }
            //On met à jour l'affichage en fonction de l'index
            $oGame = $rep->findByState(Game::PENDING_GAME, null, $nbParties * $page, $nbParties * ($page - 1));
            $rendu = $this->render(
                    'StcBundle:Game:listgame.html.twig', array(
                'listGame' => $oGame,
                'nbPages' => $nbPages,
                'page' => $page));
            $this->updateAction($request);
        }

        return $rendu;
    }

    /**
     * @Route("/controls/{idGame}/{action}", name="controls")
     */
    public function controlsAction(Request $request, $idGame, $action) {
        $rendu = $this->redirectToRoute('index');
        // On vérifie que l'utilisateur est autorisé à effectuer l'action
        if ($oUser = $this->isAuthorized($request, $idGame)) {
            $aParams = [];

            //On récupere l'id de l'user
            $idUser = $oUser->getId();

            //On récupère la partie de l'action en cours
            $rep = $this->getDoctrine()->getRepository('StcBundle:Game');
            $oGame = $rep->find($idGame);

            // Si la partie est en cours, l'utilisateur peut alors effectuer des actions
            if ($oGame->getState() == Game::CURRENT_GAME) {
                //On récupère les infos du plateau
                $oBoard = unserialize($oGame->getBoard());

                //On effectue l'action demandée suite à l'entrée clavier
                $oBoard->doAction($idUser, $action);

                //On récupère les nouvelles cases à jour
                $cases = $oBoard->getCases();

                //On recharger le nouveau plateau dans la base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($oGame);
                $oGame->setBoard(serialize($oBoard));
                $em->flush();

                $aParams['plateau'] = $cases;
            }
            $rendu = $this->render('StcBundle:Game:plateau.html.twig', $aParams);
            $this->updateAction($request, $idGame);
        }
        return $rendu;
    }

    /**
     * @Route("/player/{idGame}", name="players")
     */
    public function viewPlayersAction(Request $request, $idGame) {
        $rendu = $this->redirectToRoute('index');
        // On vérifie que l'utilisateur est autorisé à effectuer l'action
        if ($this->isAuthorized($request, $idGame)) {
            //On récupère la partie en cours
            $rep = $this->getDoctrine()->getRepository('StcBundle:Game');
            $oGame = $rep->find($idGame);
            //On renvoi les infos de la partie pour l'affichage
            $rendu = $this->render('StcBundle:Game:players.html.twig', array(
                'game' => $oGame));
        }
        //Pas de MAJ necessaire de la dernière action car déjà appelé dans controls action
        return $rendu;
    }

    //Fonction permetttant d'autorisé ou non à effectuer des actions
    //Retourn null si on n'autorise pas les actions
    //Retourne les infos du joueur si l'utilisateur connecté à l'autorisation d'effectuer des actions sur la partie donnée
    private function isAuthorized(Request $request, $idGame) {
        $permission = null;
        if ($request->getSession()->get('userStatus') == 'connected') {
            $iUser = $request->getSession()->get('userId');

            // On récupère les informations du joueur connecté
            $userRep = $this->getDoctrine()->getRepository('StcBundle:User');
            $oUser = $userRep->find($iUser);

            //On récupère la partie passée en parametre
            $rep = $this->getDoctrine()->getRepository('StcBundle:Game');
            $oGame = $rep->find($idGame);

            //On bloque au cas ou la partie n'existe pas
            if ($oGame) {
                //Si la partie contient l'utilisateur on l'autorise à faire des actions
                if ($oGame->getUsers()->contains($oUser)) {
                    $permission = $oUser;
                }
            }
        }
        return $permission;
    }

    //Fonction de mise à jour de la date de derniere action
    private function updateAction(Request $request, $idGame = null) {
        //Si on est connecté
        if ($request->getSession()->get('userStatus') == 'connected') {
            if ($idGame != null) {
                //On recherche les parties en attente de l'utilisateur inactif
                $oGame = $this->getDoctrine()->getRepository('StcBundle:Game')->find($idGame);
                if ($oGame->getState() == Game::PENDING_GAME) {
                    $users = $oGame->getUsers();
                    //On met à jour les données utilisateurs
                    foreach ($users as $user) {
                        //On récupère la date de la dernière action effectué par l'utilisateur
                        $lastAction = $user->getLastActionDate();
                        $now = new \Datetime();
                        $minutes = 5;
                        $expireDate = $lastAction->add(new \DateInterval('PT' . $minutes . 'M'));
                        //On déconnecte l'utilisateur des parties qu'il a rejoint depuis plus de x   minutes
                        //Si il n'a montré aucun signe d'activité
                        if ($expireDate < $now) {
                            $oGame->removeUser($user);
                        }
                    }
                }
            }
            //On met à jour la date de la dernière action
            $em = $this->getDoctrine()->getManager();
            $oUser = $this->getDoctrine()->getRepository('StcBundle:User')->find($request->getSession()->get('userId'));
            $oUser->setLastActionDate(new \DateTime);
            $em->flush();
            return true;
        }
        return false;
    }

}
