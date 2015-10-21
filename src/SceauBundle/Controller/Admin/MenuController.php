<?php

namespace SceauBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller
{
    /**
     * Menu Admin
     *
     */
    public function indexAction($currentRoute)
    {

        $menus = array(
            array(
                'libelle' => 'Dashboard',
                'route' => 'sceau_admin',
                'icon' => 'dashboard',
                'childMenus' => null
            ),
            array(
                'libelle' => 'Articles de presse',
                'route' => 'articles',
                'icon' => 'list',
                'childMenus' => null
            ),
            array(
                'libelle' => 'Actualités',
                'route' => 'actualites',
                'icon' => 'list',
                'childMenus' => null
            ),
            array(
                'libelle' => 'Questions',
                'route' => 'questions',
                'icon' => 'question',
                'childMenus' => null
            ),
            array(
                'libelle' => 'Litiges',
                'route' => 'litiges',
                'icon' => 'exclamation-triangle',
                'childMenus' => array(
                    array(
                        'libelle' => 'En attente d\'intervention Expert',
                        'route' => 'litiges_intervention',
                        'icon' => 'angle-double-right',
                    ),
                    array(
                        'libelle' => 'En cours - Usurpation CB',
                        'route' => 'litiges_usurpation',
                        'icon' => 'angle-double-right',
                    ),
                )

            ),

        );

        return $this->render("SceauBundle:Admin/Common:menu.html.twig", array(
            'menus' => $menus,
            'currentRoute' => $currentRoute

        ));
    }
}
