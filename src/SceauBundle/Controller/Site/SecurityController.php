<?php

namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use SceauBundle\Entity\Membre;
use SceauBundle\Entity\EnvoiEmail;
use SceauBundle\Entity\Email;
use Symfony\Component\HttpFoundation\Response;
use SceauBundle\Form\Type\Site\User\RegisterType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Contrôleur Authentification : pages relatives aux Authenfication et Inscirption
 *
 * <pre>
 * Mohammed 06/10/2015 Création
 * </pre>
 * @author Mohammed
 * @version 1.0
 * @package Site Sceau
 */
class SecurityController extends Controller
{
    /**
     * Fonction pour l'authentification
     *
     *  @Route("/login",
     *     name="site_member_login")
     * @Method("POST")
     */
    public function loginAction(Request $poRequest) 
    {
        $loResponse = null;
        if ($poRequest->isMethod('POST')) {
            $loEmail  = $poRequest->get('_email');
            $loPassword = $poRequest->get('_password');
            if (!empty($loPassword) && !empty($loEmail)) {
                try {
                    $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
                    $loLoggedUser = $loMembreLogger->logUser($loEmail,$loPassword);
                    $this->saveToken($poRequest, $loLoggedUser);
                    $loResponse = $this->forward('SceauBundle:Site/Membre:homeMembre');
                } catch(\Exception $e) {
                    dump($e->getMessage());
                   $loResponse = $this->errorAction($e->getMessage(), 'login');
                }
            }
        }
        return $loResponse;   
    }
    
    /**
     *Action pour appel l'inscription 
     *
     *  @Route("/register",
     *     name="site_member_call_register")
     * @Method("GET")
     */
    public function callRegisterAction() {
        $loUser = new Membre();
        $loForm = $this->createForm(new RegisterType(), $loUser);
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'register',
                'redirect' => '',
                'user' => null
            )
        );
    }
    
    /**
     * A l'appele du fonction lors d'erreur Login
     */
    public function errorAction($poError, $poAction) {
        $loUser = new Membre();
        $loForm = $this->createForm(new RegisterType(), $loUser);
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'register',
                'redirect' => '',
                'user' => null,
                'error' => $poError,
                'errorType' => $poAction
            )
        );
    }
    
    /**
     *Action pour l'inscription 
     *
     *  @Route("/register",
     *     name="site_member_register")
     * @Method("POST")
     */
    public function registerAction(Request $poRequest)
    {
        $loUser = new Membre();
        $loForm = $this->createForm(new RegisterType(), $loUser);
        $loManager = $this->getDoctrine()->getManager();
         if ($poRequest->isMethod('POST')) {
            $loForm->handleRequest($poRequest);
            if ($loForm->isValid()) {
                try {
                    if($poRequest->get('g-recaptcha-response') == '') {
                        return $this->errorAction('Le captcha est obligatoire', 'register');
                    } else {                
                        $loIdAvatar = $poRequest->get('AvatarID');    
                        $loFields = $poRequest->get('site_member_register');
                        $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
                       
                         $loEmail = $loMembreLogger->saveEmail($loFields);
                         $loAvatar = $loMembreLogger->saveAvatar($loIdAvatar);
                         $loUser->setAvatar($loAvatar);
                         $loUser->addEmail($loEmail);
                         $loMembreLogger->registerUser($loUser,$loFields['email']['first'],true);
                        $this->sendEmail('SceauBundle:Site/Emails:confirmationRegister.html.twig', $loUser, $loFields['email']['first']);
                        return  $this->render(
                            'SceauBundle:Site/Home:index.html.twig',
                            array(
                                'form' => $loForm->createView(),
                                'menu' => 'successRegister',
                                'user' => $loUser
                            )
                        );
                    }
                } catch (\Exception $e) {
                      dump($e->getMessage());
                    return $this->errorAction($e->getMessage(), 'register');
                }
                 
            } else {
              
                $laErrors = (string) $loForm->getErrors(true);
               // $this->errorAction($laErrors, 'errorForm');
            }
             
        }
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'register',
                'redirect' => ''
            )
        );
    }
    
    /**
    * Déconnexion de l'utilisateur.
    *
    * @Route("/logout", name="site_member_logout")
    */
    public function logoutAction()
    {
    }
  
    
    /**
     * 
     * Fonction Pour enregistrer le token pour recuperer les session connecté
     * 
     * @param type $poRequest
     * @param type $poUser
     */
    public function saveToken($poRequest, $poUser)
    {
        $loToken = new UsernamePasswordToken(
            $poUser->getPseudo(),
            $poUser->getPassword(),
            'secured_site_login_membre',
            $poUser->getRoles()
        );
        $this->get('security.token_storage')->setToken($loToken);
        $poRequest->getSession()->set('_security_secured_site_login_membre', serialize($loToken));
        $poRequest->getSession()->set('user',$poUser);
      //  $this->getUser() = $poUser;
    }
    
    
    /**
     * Fonction pour Envoyer les mails
     */
    public function sendEmail($poTemplate, $poUser, $poEmail) {
        $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
        $loManager = $this->getDoctrine()->getManager();
        $loPseudo =   $poUser->getPseudo();
        $loPwd = $loMembreLogger->displayPassword($poUser);
        try {
             $loEnvoiEmail = new EnvoiEmail();
             $loEnvoiEmail->setSubject($loPseudo . ' confirmer votre inscription sur fia-net');
             $loEnvoiEmail->setSendFrom('membres@fia-net.fr');
             $loEnvoiEmail->setSendTo($poEmail);
             $loContent =   $this->renderView(
                                $poTemplate,
                                    array(
                                        'pseudo' => $loPseudo,
                                        'email' => $poEmail,
                                        'pwd'   => $loPwd
                                    )
                                );
            $loEnvoiEmail->setContent($loContent);
            $loEnvoiEmail->setDateInsert(new \DateTime());
            $loEnvoiEmail->setDateSend(new \DateTime());
            $loEnvoiEmail->setStatus(2);
            $loManager->persist($loEnvoiEmail);
            $loManager->flush();
        } catch (Exception $ex) {
             dump($e->getMessage());
        }
    }
}
