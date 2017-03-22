<?php

namespace StcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use StcBundle\Entity\User;
use StcBundle\Form\Type\UserType;
use StcBundle\Form\Type\InscriptionType;

class UserController extends Controller {

    /**
     * @Route("/register", name = "register")
     */
    public function registerAction(Request $request) {

        // gestion formulaire d'inscription
        $oUser = new User();
        $oInscriptionForm = $this->createForm(InscriptionType::class, $oUser);
        $oInscriptionForm->handleRequest($request);
        //On regarde dans la base de données
        $em = $this->getDoctrine()->getManager();
        if ($oInscriptionForm->isSubmitted()) {
            if ($oInscriptionForm->isValid()) {
                // mise à jour de l'utilisateur
                $oUser->setPassword(sha1($oUser->getPassword()));

                // inscription en base de l'utilisateur
                $em->persist($oUser);
                $em->flush();

                // auto-login
                $request->getSession()->set('userStatus', 'connected');
                $request->getSession()->set('userName', $oUser->getLogin());
                $request->getSession()->set('userId', $oUser->getId());
                $this->addFlash('success', 'Inscription réussie !');
            } else {
                //On vérifie l'existence du login demandé dans le formulaire d'inscription
                $existLogin = (count($em->getRepository('StcBundle:User')->findByLogin($oUser->getLogin())) > 0) ? true : false;
                if ($existLogin) {
                    //On affiche une erreur si le login existe déjà
                    $this->addFlash('error', 'Le login "' . $oUser->getLogin() . '" est déjà utilisé.');
                }
                //On affiche les erreurs
                $this->displayErrorsForm($oUser);
            }

            //on redirige l'utilisateur inscrit et connecté vers l'index
            return $this->redirectToRoute('index');
        }
        return $this->render('StcBundle:User:register.html.twig', array(
                    'inscriptionForm' => $oInscriptionForm->createView()
        ));
    }

    /**
     * @Route("/login", name = "login")
     */
    public function loginAction(Request $request) {

        $oUser = new User();
        $oConnexionForm = $this->createForm(UserType::class, $oUser);

        // récupère et check les donnée du formulaire, hydrate l'objet user
        $oConnexionForm->handleRequest($request);

        if ($oConnexionForm->isSubmitted() && $oConnexionForm->isValid()) {
            // accès BDD User pour vérifier si le login saisi existe.
            $em = $this->getDoctrine()->getManager();
            $userRepository = $em->getRepository('StcBundle:User');
            $oUserLogin = $userRepository->findOneBy(array(
                'login' => $oUser->getLogin(),
                'password' => sha1($oUser->getPassword())
            ));
            // si on a trouvé un login/pwd qui correspond ds la BDD
            if ($oUserLogin instanceof User) {
                $request->getSession()->set('userStatus', 'connected');
                $request->getSession()->set('userName', $oUserLogin->getLogin());
                $request->getSession()->set('userId', $oUserLogin->getId());
            } else {
                //Si il y a une erreur à la connexion, on affiche un message visuel pour l'utilisateur
                $this->addFlash('error', 'L\'identifiant et/ou le mot de passe sont incorrectes.');
            }
            return $this->redirectToRoute('index');
        }
        return $this->render('StcBundle:User:login.html.twig', array(
                    'userForm' => $oConnexionForm->createView()
        ));
    }

    /**
     * @Route("/logout", name = "logout")
     */
    public function logoutAction(Request $request) {
        // Si l'utilisateur souhaite se déconnecter, on efface la session
        $request->getSession()->invalidate();  // on efface la session
        //Petit message informant de la deconnexion
        $this->addFlash('success', 'Vous avez bien été déconnecté !');
        // On redirige l'utilisateur sur l'accueil
        return $this->redirectToRoute('index');
    }

    private function displayErrorsForm(User $oUser) {
        //On récupère les erreurs
        $validator = $this->get('validator');
        $errors = $validator->validate($oUser);
        //Si il y a des erreurs, on les affiche
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }
    }

}
