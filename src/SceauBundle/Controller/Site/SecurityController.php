<?php

namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use SceauBundle\Entity\Membre;

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
     *Action pour l'inscription 
     *
     *  @Route("/register",
     *     name="site_member_register")
     * @Method("GET")
     */
    public function registerAction(Request $poRequest) {
        $loUser = new Membre();
        $loForm = $this->createForm(new RegisterType(), $loUser);
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'register',
            )
        );
    }
}
