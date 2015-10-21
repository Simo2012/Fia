<?php

namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use SceauBundle\Entity\Membre;
use SceauBundle\Form\Type\Site\User\RegisterType;
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
        $loForm = $this->createForm(new RegisterType(), $loUser);
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'tombola',
                'call' => 1
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
                return $this->render(
                'SceauBundle:Site/Home:index.html.twig',
                    array(
                        'participation' => $loNumberParticipation,
                        'menu' => 'tombola',
                        'call' => 2
                    )
                );
            }
        }
        return $this->render("SceauBundle:Site/Security:test.html.twig");   
    }
    
    
}
