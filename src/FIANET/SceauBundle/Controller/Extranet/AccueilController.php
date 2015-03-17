<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AccueilController extends Controller
{
    /**
     * Affiche la page d'accueil de l'extranet.
     *
     * @Route("/", name="extranet_accueil")
     * @Method("GET")
     * @Template("FIANETSceauBundle:Extranet/Accueil:index.html.twig")
     */
    public function indexAction()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('accueil')->setCurrent(true);

        return array();
    }
}
