<?php

namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SceauBundle\Form\Type\Site\User\RegisterType;

/**
 * Contrôleur Membre : pages relatives aux action de membre
 *
 * <pre>
 * Mohammed 28/10/2015 Création
 * </pre>
 * @author Mohammed
 * @version 1.0
 * @package Site Sceau
 */
class MembreController extends Controller
{
    
    /**
    * Action pour l'appel du home membre
    * 
    * @Route("/membre/home",
    *     name="site_home_membre")
    * @Method("GET")
    */
    public function homeMembreAction()
    {
        $loPseudoMembre =  $this->get('security.context')->getToken()->getUser();
        $loManager = $this->getDoctrine()->getManager();
        if ($loPseudoMembre != 'anon.') { 
            $loUser = $loManager->getRepository('SceauBundle:Membre')->getByPseudo($loPseudoMembre);
            $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
            $loPourcentage = $loMembreLogger->getPourcentage($loUser);
            $loNewletters = $loManager->getRepository('SceauBundle:Newsletters')->getLastNewsLetters(1);
            /** Envoyer vers la page NewsLetter **/
            return $this->render("SceauBundle:Site/Home:index.html.twig",
                    array(
                        'newsletters' => $loNewletters, 
                        'menu' => 'home-membre',
                        'user' => $loUser, 
                        'pourcentage' => $loPourcentage
                    )
                );
        }
        $loResponse = $this->forward('SceauBundle:Site/Home:index');
        return $loResponse;
    }
    
    /**
    * Action pour modifier les information d'un compte 
    *  @Route("/membre/home/compte",
    *     name="site_home_membre_compte")
    * @Method("GET")
    */
    public function updateMembreAction()
    {
        $loPseudoMembre =  $this->get('security.context')->getToken()->getUser();
        $loManager = $this->getDoctrine()->getManager();
        if ($loPseudoMembre != 'anon.') { 
            $loUser = $loManager->getRepository('SceauBundle:Membre')->getByPseudo($loPseudoMembre);
            $loForm = $this->createForm(new RegisterType(), $loUser);
            return $this->render(
                'SceauBundle:Site/Home:index.html.twig',
                array(
                    'form' => $loForm->createView(),
                    'menu' => 'update-compte',
                    'redirect' => '',
                    'user' => $loUser
                )
            );
        }
        $loResponse = $this->forward('SceauBundle:Site/Home:index');
        return $loResponse;
    }
}
