<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use FIANET\SceauBundle\Form\Type\SiteIdType;
use FIANET\SceauBundle\Validator\Constraints\SiteId;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthentificationController extends Controller
{
    /**
     * Affichage du formulaire de connexion de l'extranet.
     *
     * @Route("/login", name="extranet_utilisateur_login")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        return $this->render('FIANETSceauBundle:Extranet:login_form.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error,
        ));
    }

    /**
     * Authentification de l'utilisateur après soumission du formulaire de connexion à l'extranet.
     *
     * @Route("/login_check", name="extranet_utilisateur_login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * Déconnexion de l'utilisateur.
     *
     * @Route("/logout", name="extranet_utilisateur_logout")
     */
    public function logoutAction()
    {
    }

    /**
     * Affichage et gestion du formulaire de mot de passe oublié.
     *
     * @Route("/mdp_oublie", name="extranet_utilisateur_mdp_oublie")
     * @Method({"GET", "POST"})
     * @Template("FIANETSceauBundle:Extranet:login_mdp_oublie.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function motDePasseOublieAction(Request $request)
    {
        $form = $this->createForm(new SiteIdType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            /* TODO : si site ID existe, on appelle Open AM. Si Open AM OK, on redirige vers page de confirmation */
            return $this->redirect($this->generateUrl('extranet_utilisateur_mdp_oublie_conf'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Affichage de la confirmation du formulaire de mot de passe oublié.
     *
     * @Route("/mdp_oublie_confirmation", name="extranet_utilisateur_mdp_oublie_conf")
     * @Method("GET")
     * @Template("FIANETSceauBundle:Extranet:login_mdp_oublie_conf.html.twig")
     *
     * @return array
     */
    public function confMotDePasseOublieAction()
    {
        return array();
    }
}
