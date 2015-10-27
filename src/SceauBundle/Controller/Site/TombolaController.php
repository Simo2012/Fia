<?php

namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use SceauBundle\Entity\Membre;
use SceauBundle\Entity\Avatar;
use SceauBundle\Form\Type\Site\User\RegisterType;
use Symfony\Component\HttpFoundation\Response;
/**
 * Contrôleur Tombola : pages relatives aux Tombola de site web
 *
 * <pre>
 * Mohammed 20/10/2015 Création
 * </pre>
 * @author Mohammed
 * @version 1.0
 * @package Site Sceau
 */
class TombolaController extends Controller
{
    
    /**
    * Action pour l'appel du Participation a tombola
    * 
    * @Route("/tombola",
    *     name="site_home_tombola")
    * @Method("GET")
    */
    public function callTombolaAction()
    {
        $loUser = new Membre();
        $loForm = $this->createForm(new RegisterType(), $loUser, 
                        array('attr' => array('tombola' => 1)));
        $loTombola = $this->container->get('sceau.site.user.user_tombola');
        $loWinner = $loTombola->getWinner();
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'tombola',
                'call' => 1,
                'redirect' => '',
                'winners'   => $loWinner
            )
        );
    }
    
    /**
    * Action pour l'appel du Participation a tombola
    * 
    * @Route("/tombola/login",
    *     name="site_tombola_login")
    * @Method("POST")
    */
    public function loginTombolaAction(Request $poRequest) 
    {
        if ($poRequest->isMethod('POST')) {
            $loEmail  = $poRequest->get('email');
            $loPassword = $poRequest->get('password');
            if (!empty($loPassword) && !empty($loEmail)) {
                $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
                $loUser = $loMembreLogger->logUser($loEmail,$loPassword);
                $loNumberParticipation = $loMembreLogger->particpateTombola($loUser, 1);
                $loTombola = $this->container->get('sceau.site.user.user_tombola');
                $loWinner = $loTombola->getWinner();
                return $this->render(
                    'SceauBundle:Site/Home:index.html.twig',
                    array(
                        'participation' => $loNumberParticipation,
                        'menu' => 'tombola',
                        'call' => 2,
                         'winners'   => $loWinner
                    )
                );
            }
        }
        return $this->render("SceauBundle:Site/Security:test.html.twig");   
    }
    
    /**
    * Action pour l'appel du Participation a tombola
    * 
    * @Route("/tombola/register",
    *     name="site_member_register_tombola")
    * @Method("POST")
    */
    public function RegitreTombolaAction(Request $poRequest)
    {
        $loUser = new Membre();
        $loForm = $this->createForm(new RegisterType(), $loUser);
        $loManager = $this->getDoctrine()->getManager();
       // $recaptcha = $this->createForm($this->get('security.authentication.listener.form'));
         if ($poRequest->isMethod('POST')) {
            $loForm->handleRequest($poRequest);
                
                $loIdAvatar = $poRequest->get('AvatarID');
                $loFields = $poRequest->get('site_member_register');
                
                $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
                 
                if (is_numeric($loIdAvatar)) {
                    $loAvatar = new Avatar();
                    $loAvatar->setNumber($loIdAvatar);
                    $loUser->setAvatar($loAvatar);
                    $loManager->persist($loAvatar);
                }
                $loEmails = $loMembreLogger->saveEmail($loFields);
                $loCoordonnes = $loMembreLogger->saveCoordonnées($loFields);
                $loUser->addEmail($loEmails);
                $loUser->setCoordonnee($loCoordonnes);
                $loUser = $loMembreLogger->registerUser($loUser,$loFields['email']['first'],true);
                $loNumberParticipation = $loMembreLogger->particpateTombola($loUser, 1);
                $loTombola = $this->container->get('sceau.site.user.user_tombola');
                $loWinner = $loTombola->getWinner();
                return $this->render(
                    'SceauBundle:Site/Home:index.html.twig',
                    array(
                        'participation' => $loNumberParticipation,
                        'menu' => 'tombola',
                        'call' => 2,
                        'winners'   => $loWinner
                    )
                );
            }else {
               $laErrors = (string) $loForm->getErrors(true);
               return new Response(json_encode(array('status' => 'KO', 'error' => $laErrors)));
             
        }
        
    }
    
}
