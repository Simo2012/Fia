<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use FIANET\SceauBundle\Exception\Extranet\AccesInterditException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class QuestionnairesController extends Controller
{
    /**
     * Affiche le tableau de bord des questionnaires.
     *
     * @Route("/questionnaires", name="extranet_questionnaires")
     * @Method("GET")
     */
    public function indexAction()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.dashboard')->setCurrent(true);

        return $this->render('FIANETSceauBundle:Extranet/Questionnaires:index.html.twig');
    }

    /*** TODO : c'est juste pour faire fonctionner le menu, à faire correctement par la suite ***/

    /**
     *
     * @Route("/questionnaires/questionnaires", name="extranet_questionnaires_questionnaires")
     * @Method("GET")
     */
    public function x1Action()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        $menu->getChild('questionnaires')->getChild('questionnaires.questionnaires')->setCurrent(true);
        
        return $this->render('FIANETSceauBundle:Extranet/Questionnaires:index.html.twig');
    }

    /**
     *
     * @Route("/questionnaires/questions-personnalisées", name="extranet_questionnaires_questions_personnalisees")
     * @Method("GET")
     */
    public function x2Action()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        
        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.questions_personnalisees');
        $elementMenu->setCurrent(true);
        
        if(!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        return $this->render('FIANETSceauBundle:Extranet/Questionnaires:index.html.twig');
    }

    /**
     *
     * @Route("/questionnaires/relance-questionnaires", name="extranet_questionnaires_relance_questionnaires")
     * @Method("GET")
     */
    public function x3Action()
    {
        $menu = $this->get('fianet_sceau.extranet.menu');
        
        $elementMenu = $menu->getChild('questionnaires')->getChild('questionnaires.relance_questionnaires');
        $elementMenu->setCurrent(true);
        
        if(!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }

        return $this->render('FIANETSceauBundle:Extranet/Questionnaires:index.html.twig');
    }
}

