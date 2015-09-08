<?php

namespace SceauBundle\Service\Extranet;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilder
{
    private $factory;
    private $em;
    private $translator;
    private $session;
    private $menuAcces;

    public function __construct(
        FactoryInterface $factory,
        EntityManager $em,
        TranslatorInterface $translator,
        Session $session,
        MenuAcces $menuAcces
    ) {
        $this->factory = $factory;
        $this->em = $em;
        $this->translator = $translator;
        $this->session = $session;
        $this->menuAcces = $menuAcces;
    }

    /**
     * Créee le menu de l'extranet en fonction du site.
     * On parcourt les éléments parents. Pour chaque élément parent, on parcourt ses éléments fils.
     * Chaque élément est ajouté à l'arborescence du menu.
     *
     * @return MenuItem
     */
    public function creerMenu()
    {
        $site = $this->em->merge($this->session->get('siteSelectionne'));

        $menuElements = $this->em->getRepository('SceauBundle:Extranet\MenuElement')
            ->menuExtranetCompletPourUnSite($site);

        $menu = $this->factory->createItem('root');

        /* TODO : ici on a l'ensemble des éléments du menu avec leur option éventuelle à posséder.
        On a également l'ensemble des infos du site : packages, options souscrites.
        Il ne reste donc plus qu'à comparer l'ensemble des infos en PHP en parcourant les objets (et non en requête)
        via un service générique de contrôle des droits, on en aura surement besoin autre part -> arguments : site,
        option,...
        L'algo en gros :
        si l'élément du menu nécessite une option :
        1) On regarde si elle est comprise de base dans le package du site  => OK, on affiche.
        2) Si elle est souscriptible, on regarde si elle est présente dans les options souscrites par le site
            => OK, on affiche
        3) Tous les autres cas => KO, on ajoute un "extra" pour cet élément.
         */

        for ($numMenuElement = 0; $numMenuElement < count($menuElements); $numMenuElement++) {
            $menuElementParent = $menu->addChild(
                $menuElements[$numMenuElement]->getNom(),
                array('route' => $menuElements[$numMenuElement]->getRoute())
            );
            $menuElementParent->setLabel('');
            $menuElementParent->setLinkAttribute('id', $menuElements[$numMenuElement]->getNom());
            $menuElementParent->setCurrent(false);

            foreach ($menuElements[$numMenuElement]->getMenuElementsFils() as $menuElement) {
                $menuElementFils = $menuElementParent->addChild(
                    $menuElement->getNom(),
                    array('route' => $menuElement->getRoute())
                );

                $menuElementFils->setLabel(
                    $this->translator->trans($menuElement->getLibelle(), array(), 'extranet_menu')
                );

                $menuElementFils->setExtra('accesAutorise',$this->menuAcces->donnerAcces($site, $menuElement->getOption()));

                $descriptifAcces = $this->translator->trans($menuElement->getLibelle(), array(), 'extranet_menu_descriptif');

                $menuElementFils->setExtra('accesDescriptif',$descriptifAcces);

                $menuElementFils->setCurrent(false);
            }
        }

        return $menu;
    }
}
