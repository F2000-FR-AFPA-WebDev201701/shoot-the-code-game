<?php

namespace StcBundle\Controller;

# Pour les annotations @Template si le nom de la fonction = le nom de la vue,
# sinon @Template(name="@STC/Game/maVue.html.twig

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use StcBundle\Form\ContactType;
use StcBundle\Form\InscriptionType;

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

        // gestion formulaire de connexion
//        $oUserForm = $this->createForm(UserType::class);
//        $oUserForm->handleRequest($request);
//        if ($oUserForm->isSubmitted() && $oUserForm->isValid()) {
//            // dump($oUserForm->getData());
//            // prévoir la connexion de l'utilisateur
//        }

        return $this->render('StcBundle:Game:index.html.twig', array(
                    'inscriptionForm' => $oInscriptionForm->createView(),
                    'contactForm' => $oContactForm->createView()));
    }

}
