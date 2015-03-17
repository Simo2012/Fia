<?php

namespace FIANET\SceauBundle\Service\Extranet;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;

class MenuAcces {
  
    private $em;
    private $securityContext;

    public function __construct(
        EntityManager $em, 
        SecurityContext $securityContext
    ) {
        $this->em = $em;    
        $this->securityContext = $securityContext;
    }

    /**
     * Droit d'accès à une page de l'extranet
     * Permet à partir d'un élément de menu, du package souscrit et de l'option liée (s'il y en a une)
     * de savoir si un site peut accéder à une page.
     * 
     * On regarde à quelle option appartient l'élément de menu sélectionné :
     *  si l'élément n'est lié à aucune option, on retourne true;
     *  si l'élément n'est lié ni à une option de base ni à une option souscrite, on retourne false sinon on retourne true.
     * 
     * @param String $name Nom de l'élément du menu
     * 
     * @return boolean
     */
    public function donnerAcces($name)
    {
        $optionIdElementActif = '';
        
        $elementActif = $this->em->getRepository('FIANETSceauBundle:Extranet\MenuElement')
                ->findOneByNom($name);
        
        if($elementActif) {
            if($elementActif->getOption()) {
                
                $optionIdElementActif = $elementActif->getOption();
                
                $site = $this->securityContext->getToken()->getUser()->getSite();
                
                $packageOptions = $this->em->getRepository('FIANETSceauBundle:PackageOption');
                
                if ($packageOptions->optionDeBase($site, $optionIdElementActif)) {
                    return true;
                } else if ($packageOptions->optionSouscriptible($site, $optionIdElementActif)) {
                    $siteOptions = $this->em->getRepository('FIANETSceauBundle:SiteOption');
                    
                    if ($siteOptions->optionSouscrite($site, $optionIdElementActif)) {
                        return true;
                    } else {
                        return false;
                    }
                } else { // Option non souscriptible ou non souscrite
                    return false;
                }
            } else { // Elément non lié à une option
                return true;
            }
        } else { // N'est pas un élément de menu
            throw new \Exception("Elément de menu non trouvé");
        }
    }
}
