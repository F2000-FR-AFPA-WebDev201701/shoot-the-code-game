<?php

namespace StcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use StcBundle\Entity\User;
use StcBundle\Form\UserType;

class UserController extends Controller {

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
            $userName = $oUser->getLogin();
            $userPwd = $oUser->getPassword();
            //$userPwd = sha1($oConnexionUser->getPassword());
            $em = $this->getDoctrine()->getManager();
            $userRepository = $em->getRepository('StcBundle:User');
            $userRepository->findOneBy(array(
                'login' => $userName,
                'password' => $userPwd));
// si on a trouvé un login/pwd qui correspond ds la BDD
            if ($oUser instanceof User) {
                $request->getSession()->set('userStatus', 'connected');
                $request->getSession()->set('userName', $userName);
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
