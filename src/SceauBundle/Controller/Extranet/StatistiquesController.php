<?php

namespace SceauBundle\Controller\Extranet;

use SceauBundle\Exception\Extranet\AccesInterditException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StatistiquesController extends Controller
{
    /**
     * Affiche le tableau de bord de la partie Statistiques.
     *
     * @Route("/statistiques", name="extranet_statistiques")
     * @Method("GET")
     */
    public function indexAction()
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('statistiques')->getChild('statistiques.dashboard')->setCurrent(true);

        return $this->render('SceauBundle:Extranet/Statistiques:index.html.twig');
    }

    /*** TODO : c'est juste pour faire fonctionner le menu, à faire correctement par la suite ***/

    /**
     * @Route("/statistiques/avant-livraison", name="extranet_statistiques_avant_livraison")
     * @Method("GET")
     */
    public function x1Action()
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('statistiques')->getChild('statistiques.avant_livraison')->setCurrent(true);

        return $this->render('SceauBundle:Extranet/Statistiques:index.html.twig');
    }

    /**
     * @Route("/statistiques/après-livraison", name="extranet_statistiques_apres_livraison")
     * @Method("GET")
     */
    public function x2Action()
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('statistiques')->getChild('statistiques.apres_livraison')->setCurrent(true);

        return $this->render('SceauBundle:Extranet/Statistiques:index.html.twig');
    }

    /**
     * @Route("/statistiques/vos-acheteurs", name="extranet_statistiques_vos_acheteurs")
     * @Method("GET")
     */
    public function x3Action()
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('statistiques')->getChild('statistiques.vos_acheteurs')->setCurrent(true);

        return $this->render('SceauBundle:Extranet/Statistiques:index.html.twig');
    }

    /**
     * @Route("/statistiques/questions-personnalisées", name="extranet_statistiques_questions_personnalisees")
     * @Method("GET")
     */
    public function x4Action()
    {
        $menu = $this->get('sceau.extranet.menu');
        
        $elementMenu = $menu->getChild('statistiques')->getChild('statistiques.questions_personnalisees');
        $elementMenu->setCurrent(true);
        
        if (!$elementMenu->getExtra('accesAutorise')) {
            throw new AccesInterditException($elementMenu->getLabel(), $elementMenu->getExtra('accesDescriptif'));
        }
        
        return $this->render('SceauBundle:Extranet/Statistiques:index.html.twig');
    }

    /**
     * @Route("/statistiques/vos-données", name="extranet_statistiques_vos_donnees")
     * @Method("GET")
     */
    public function x5Action()
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('statistiques')->getChild('statistiques.vos_donnees')->setCurrent(true);

        return $this->render('SceauBundle:Extranet/Statistiques:index.html.twig');
    }
}
