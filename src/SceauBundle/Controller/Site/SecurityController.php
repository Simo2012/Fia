<?php

namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use SceauBundle\Entity\Membre;
use SceauBundle\Entity\Avatar;
use SceauBundle\Entity\Email;

use Symfony\Component\HttpFoundation\Response;
use SceauBundle\Form\Type\Site\User\RegisterType;

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
        if ($poRequest->isMethod('POST')) {
            $loEmail  = $poRequest->get('loginemail');
            $loPassword = $poRequest->get('password');
            if (!empty($loPassword) && !empty($loEmail)) {
                $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
                $loMembreLogger->logUser($loEmail,$loPassword);
            }
        }
        return $this->render("SceauBundle:Site/Security:test.html.twig");   
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
                'redirect' => ''
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
       // $recaptcha = $this->createForm($this->get('security.authentication.listener.form'));
         if ($poRequest->isMethod('POST')) {
            $loForm->handleRequest($poRequest);
            if ($loForm->isValid()) {
                $loIdAvatar = $poRequest->get('AvatarID');
                $loEmails = $poRequest->get('site_member_register');
                if (is_numeric($loIdAvatar)) {
                    $loAvatar = new Avatar();
                    $loAvatar->setNumber($loIdAvatar);
                    $loUser->setAvatar($loAvatar);
                    $loManager->persist($loAvatar);
                }
                $loEmail = new Email();
                $loEmail->setEmail($loEmails['email']['first']);
                $loEmail->setPrincipal(true);
                $loEmail->setDateConfirmation(new \DateTime());
                $loUser->addEmail($loEmail);
                
                $loManager->persist($loEmail);
                $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
                $loMembreLogger->registerUser($loUser,$loEmails['email']['first'],true);
                return $this->render("SceauBundle:Site/Security:test.html.twig");
            } else {
               $laErrors = (string) $loForm->getErrors(true);
               return new Response(json_encode(array('status' => 'KO', 'error' => $laErrors)));
            }
             
        }
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'register',
           //     'recaptcha' => $recaptcha->createView()
            )
        );
    }
}