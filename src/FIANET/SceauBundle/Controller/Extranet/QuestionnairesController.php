<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class QuestionnairesController extends Controller
{
    /**
     * Affiche le tableau de bord des questionnaires.
     *
     * @Route("/questionnaires", name="extranet_questionnaires")
     * @Method("GET")
     * @Template("FIANETSceauBundle:Extranet/Questionnaires:index.html.twig")
     */
    public function indexAction()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.dashboard')->setCurrent(true);

        return array();
    }

    /*** TODO : c'est juste pour faire fonctionner le menu, à faire correctement par la suite ***/

    /**
     *
     * @Route("/questionnaires/questionnaires", name="extranet_questionnaires_questionnaires")
     * @Method("GET")
     * @Template("FIANETSceauBundle:Extranet/Questionnaires:index.html.twig")
     */
    public function x1Action()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);

        return array();
    }

    /**
     *
     * @Route("/questionnaires/questions-personnalisées", name="extranet_questionnaires_questions_personnalisees")
     * @Method("GET")
     * @Template("FIANETSceauBundle:Extranet/Questionnaires:index.html.twig")
     */
    public function x2Action()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        
        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.questions_personnalisees');
        $elementMenu->setCurrent(true);
        
        //$accesElementMenu = $this->get('fianet_sceau.extranet.menu_acces');
        //if (!$accesElementMenu->donnerAcces($elementMenu->getName())) {
        if(!$elementMenu->getExtra('accesAutorise')) { // inconvénient : va exécuter trop de requêtes
            
            $content = $this->renderView(
                'FIANETSceauBundle:Extranet:acces_refuse.html.twig',
                array('elementMenuTitre' => $elementMenu->getLabel(),
                    'elementMenuDescriptif' => $elementMenu->getExtra('accesDescriptif')
                    )
            );
            
            return new Response($content);
        }
        
        return array();
    }

    /**
     *
     * @Route("/questionnaires/relance-questionnaires", name="extranet_questionnaires_relance_questionnaires")
     * @Method("GET")
     * @Template("FIANETSceauBundle:Extranet/Questionnaires:index.html.twig")
     */
    public function x3Action()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        
        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.relance_questionnaires');
        $elementMenu->setCurrent(true);
        
        //$accesElementMenu = $this->get('fianet_sceau.extranet.menu_acces');
        //if (!$accesElementMenu->donnerAcces($elementMenu->getName())) {
        if(!$elementMenu->getExtra('accesAutorise')) { // inconvénient : va exécuter trop de requêtes
            
            
            $content = $this->renderView(
                'FIANETSceauBundle:Extranet:acces_refuse.html.twig',
                array('elementMenuTitre' => $elementMenu->getLabel(),
                    'elementMenuDescriptif' => $elementMenu->getExtra('accesDescriptif')
                    )
            );
            
            return new Response($content);
        }

        return array();
    }
}

