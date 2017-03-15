<?php

namespace StcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use StcBundle\Entity\User;
use StcBundle\Form\UserType;
use StcBundle\Form\InscriptionType;

class UserController extends Controller {

    /**
     * @Route("/register", name = "register")
     */
    public function registerAction(Request $request) {

        // gestion formulaire d'inscription
        $oInscriptionForm = $this->createForm(InscriptionType::class);
        $oInscriptionForm->handleRequest($request);
        if ($oInscriptionForm->isSubmitted() && $oInscriptionForm->isValid()) {
            // création de l'utilisateur
            $formData = $oInscriptionForm->getData();
            $oUser = new user();
            $oUser->setLogin($formData['login']);
            $oUser->setPassword(sha1($formData['password']));
            $oUser->setMail($formData['mail_inscription']);

            // inscription en base de l'utilisateur
            $em = $this->getDoctrine()->getManager();
            $em->persist($oUser);
            $em->flush();

            // auto-login
            $request->getSession()->set('userStatus', 'connected');
            $request->getSession()->set('userName', $oUser->getLogin());
            $request->getSession()->set('userId', $oUser->getId());

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

        $oUser = new user();
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
        // On redirige l'utilisateur sur l'accueil
        return $this->redirectToRoute('index');
    }

}
