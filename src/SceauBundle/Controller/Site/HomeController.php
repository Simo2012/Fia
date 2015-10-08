<?php

namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Contrôleur Home : pages relatives aux home de site web
 *
 * <pre>
 * Mohammed 06/10/2015 Création
 * </pre>
 * @author Mohammed
 * @version 1.0
 * @package Site Sceau
 */
class HomeController extends Controller
{
    /**
     * Afficher la page home.
     *
     * @Route("/", name="site_home")
     *  @Route("/home/operation",
     *     name="site_operation_detail")
     * @Method("GET")
     */
    public function indexAction() 
    {
       $loManager = $this->getDoctrine()->getManager();
       //Recuperer les categories disponibles
       $loCategories = $loManager->getRepository('SceauBundle:Categorie')->getActifCategories();
       return $this->render("SceauBundle:Site/Home:index.html.twig", array('categories' => $loCategories, 'menu' => 'home'));
    }
}
