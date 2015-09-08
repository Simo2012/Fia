<?php

namespace SceauBundle\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AccueilController extends Controller
{
    /**
     * Affiche la page d'accueil de l'extranet.
     *
     * @Route("/", name="extranet_accueil")
     * @Method("GET")
     */
    public function indexAction()
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('accueil')->setCurrent(true);

        return $this->render("SceauBundle:Extranet/Accueil:index.html.twig");
    }
}
