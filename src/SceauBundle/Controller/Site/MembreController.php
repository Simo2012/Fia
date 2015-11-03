<?php

namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SceauBundle\Form\Type\Site\User\UpdateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $loPseudoMembre = $this->get('security.context')->getToken()->getUser();
        $loManager = $this->getDoctrine()->getManager();
        if ($loPseudoMembre != 'anon.') {
            $loUser = $loManager->getRepository('SceauBundle:Membre')->getByPseudo($loPseudoMembre);
            $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
            $loPourcentage = $loMembreLogger->getPourcentage($loUser);
            $loNewletters = $loManager->getRepository('SceauBundle:Newsletters')->getLastNewsLetters(1);

            /** Envoyer vers la page NewsLetter **/
            return $this->render(
                "SceauBundle:Site/Home:index.html.twig",
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
     * @Route("/membre/home/compte",
     *name="site_home_membre_compte")
     * @Method("GET")
     */
    public function callUpdateCompteAction()
    {
        //$this->get('session')->set('success', null);
        $loResponse = $this->forward('SceauBundle:Site/Membre:updateCompteMembre');
        return $loResponse;
    }

    /**
     * Action pour modifier les information d'un compte
     * @Route("/membre/home/update_compte",
     *     name="site_member_update")
     * @Method("POST")
     */
    public function updateCompteMembreAction()
    {
        dump($this->get('session')->get('success'));
        dump($this->get('session')->get('confirmation'));
        $loPseudoMembre = $this->get('security.context')->getToken()->getUser();
        $loManager = $this->getDoctrine()->getManager();
        if ($loPseudoMembre != 'anon.') {
            $loUser = $loManager->getRepository('SceauBundle:Membre')
                ->getByPseudo($loPseudoMembre);
            $loEmailSecondaire = $loManager->getRepository('SceauBundle:Membre')
                ->getEmailsSecondaires($loUser->getId());
            $loForm = $this->createForm(new UpdateType(), $loUser);
            $loRequest = $this->get('request_stack')->getCurrentRequest();
            if ($loRequest->isMethod('POST')) {
                $loForm->bind($loRequest); // TODO : Deprecated !
                $loManager->flush($loUser->getCoordonnee());
                $loManager->flush($loUser);
            }

            return $this->render(
                'SceauBundle:Site/Home:index.html.twig',
                array(
                    'form' => $loForm->createView(),
                    'menu' => 'update-compte',
                    'redirect' => '',
                    'user' => $loUser,
                    'emails' => $loEmailSecondaire
                )
            );
        }
        $loResponse = $this->forward('SceauBundle:Site/Home:index');
        return $loResponse;
    }

    /**
     * Action pour ajouter un Email
     * @Route("/membre/home/add_Email",
     *     name="site_member_add_email")
     * @Method("POST")
     *
     * @param Request $poRequest
     *
     * @return Response
     */
    public function addEmailMembre(Request $poRequest)
    {
        $loPseudoMembre = $this->get('security.context')->getToken()->getUser();
        $loManager = $this->getDoctrine()->getManager();
        $loUser = $loManager->getRepository('SceauBundle:Membre')->getByPseudo($loPseudoMembre);
        $loEmailSecondaire = $loManager->getRepository('SceauBundle:Membre')->getEmailsSecondaires($loUser->getId());
        $loForm = $this->createForm(new UpdateType(), $loUser);
        //$loRequest = $this->get('request_stack')->getCurrentRequest();
        if ($poRequest->isMethod('POST')) {
            $loField = $poRequest->get('email');
            $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
            $loEmail = $loMembreLogger->saveEmailSecondaire($loField);
            $loUser->addEmail($loEmail);
            $loManager->flush($loUser);
            $this->get('session')->set('confirmation', 'OK');
            $this->get('session')->set('success', 'AjoutEmail');
        }
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'update-compte',
                'redirect' => '',
                'user' => $loUser,
                'emails' => $loEmailSecondaire
            )
        );
    }


    /**
     * Action pour Suprimer un Email
     * @Route("/membre/home/delete_Email",
     *     name="site_member_delete_email")
     * @Method("POST")
     *
     */
    public function deleteEmailMembre()
    {

        $loPseudoMembre = $this->get('security.context')->getToken()->getUser();
        $loManager = $this->getDoctrine()->getManager();
        $loRequest = $this->get('request_stack')->getCurrentRequest();
        $loUser = $loManager->getRepository('SceauBundle:Membre')->getByPseudo($loPseudoMembre);
        $loForm = $this->createForm(new UpdateType(), $loUser);
        if ($loRequest->isMethod('POST')) {
            $loIdEmail = $loRequest->get('idEmail');
            $loEmail = $loManager->getRepository('SceauBundle:Email')->find($loIdEmail);
            $loUser->removeEmail($loEmail);
            $loManager->flush($loUser);
            var_dump($loUser);
            $loManager->remove($loEmail);
            $loManager->flush();
        }
        $loEmailSecondaire = $loManager->getRepository('SceauBundle:Membre')->getEmailsSecondaires($loUser->getId());
        $this->get('session')->set('confirmation', 'OK');
        $this->get('session')->set('success', 'AjoutEmail');
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'update-compte',
                'redirect' => '',
                'user' => $loUser,
                'emails' => $loEmailSecondaire
            )
        );
    }

    /**
     * Action pour Suprimer un Email
     * @Route("/membre/home/update_email",
     *     name="site_member_update_email_principale")
     * @Method("POST")
     *
     */
    public function updateEmailPrincipaleMembre()
    {
        $loPseudoMembre = $this->get('security.context')->getToken()->getUser();
        $loManager = $this->getDoctrine()->getManager();
        $loRequest = $this->get('request_stack')->getCurrentRequest();
        $loUser = $loManager->getRepository('SceauBundle:Membre')->getByPseudo($loPseudoMembre);
        $loForm = $this->createForm(new UpdateType(), $loUser);
        if ($loRequest->isMethod('POST')) {
            $loField = $loRequest->get('email');
            $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
            $loMembreLogger->saveEmailPrincipale($loField, $loUser);
        }
        $loEmailSecondaire = $loManager->getRepository('SceauBundle:Membre')->getEmailsSecondaires($loUser->getId());
        $this->get('session')->set('confirmation', 'OK');
        $this->get('session')->set('success', 'EmailPrincipale');
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'update-compte',
                'redirect' => '',
                'user' => $loUser,
                'emails' => $loEmailSecondaire
            )
        );
    }

    /**
     * Action pour Suprimer un Email
     * @Route("/membre/home/check_pwd",
     *     name="site_member_check_pwd")
     * @Method("POST")
     *
     */
    public function checkPwd()
    {
        $loPseudoMembre = $this->get('security.context')->getToken()->getUser();
        $loManager = $this->getDoctrine()->getManager();
        $loRequest = $this->get('request_stack')->getCurrentRequest();
        $loUser = $loManager->getRepository('SceauBundle:Membre')->getByPseudo($loPseudoMembre);
        $lsResponse = null;
        if ($loRequest->isMethod('POST')) {
            $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
            $loResponse = $loMembreLogger->checkpwd($loUser, $loRequest->get('pwd'));
            $lsResponse = array('error' => $loResponse);

        }

        $laResponse = new Response(json_encode($lsResponse));
        return new Response($laResponse, 200, array('Content-Type' => 'application/json'));
    }


    /**
     * Action modifier Password
     * @Route("/membre/home/update_pwd",
     *     name="site_membre_update_pwd")
     * @Method("POST")
     *
     */
    public function updatePwd()
    {
        $loPseudoMembre = $this->get('security.context')->getToken()->getUser();
        $loManager = $this->getDoctrine()->getManager();
        $loRequest = $this->get('request_stack')->getCurrentRequest();
        $loUser = $loManager->getRepository('SceauBundle:Membre')->getByPseudo($loPseudoMembre);
        $loForm = $this->createForm(new UpdateType(), $loUser);
        if ($loRequest->isMethod('POST')) {
            $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
            $loField = array(
                'password_actuel' => $loRequest->get('password_actuel'),
                'password_nouveau' => $loRequest->get('password_nouveau'),
                'conf_password_nouveau' => $loRequest->get('conf_password_nouveau')
            );
            //dump($loRequest->get('site_membre_update_pwd'));
            //$loField =  $loRequest->get('form_password');
            $loMembreLogger->updatePwd($loUser, $loField);
        }
        $loEmailSecondaire = $loManager->getRepository('SceauBundle:Membre')->getEmailsSecondaires($loUser->getId());
        $this->get('session')->set('confirmation', 'OK');
        $this->get('session')->set('success', 'Passwrod');
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'update-compte',
                'redirect' => '',
                'user' => $loUser,
                'emails' => $loEmailSecondaire
            )
        );
    }
}
