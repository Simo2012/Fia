<?php

namespace SceauBundle\Service\Extranet;

use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\Option;
use SceauBundle\Entity\Site;

class MenuAcces
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Droit d'accès à une page de l'extranet
     * Permet à partir d'un élément de menu, du package souscrit et de l'option liée (s'il y en a une)
     * de savoir si un site peut accéder à une page.
     *
     * On regarde à quelle option appartient l'élément de menu sélectionné :
     * - si l'élément n'est lié à aucune option, on retourne true;
     * - si l'élément n'est lié ni à une option de base ni à une option souscrite, on retourne false
     * sinon on retourne true.
     *
     * @param Site $site Instance de Site
     * @param Option $option Instance de Option
     *
     * @return boolean
     */
    public function donnerAcces($site, $option)
    {
        /* TODO après confirmation de la MOA/CRM, il faudra revoir entièrement cette méthode ainsi que les entités
         liées aux packages/options Sceau dans le lot2 ou lot3 (check des attributs 'actif' à effectuer également) */

        if ($option) {
            $oPO = $site->getPackage()->getPackageOptions();
            $optionId = $option->getId();
            $dateDuJour = new \DateTime;
            // TODO : format de date à revoir (avec tz)

            for ($numPO = 0; $numPO < count($oPO); $numPO++) {
                if ($oPO[$numPO]->getOption()->getId() === $optionId) {
                    if ($oPO[$numPO]->getOptionType()->getId() === 1) {
                        /* TODO après confirmation de la MOA/CRM, on n'aura plus à regarder l'OptionType, on se
                         basera sur un attribut booléen nommé "base" de l'entité PackageOption */
                        return true; // Option de base

                    } else {
                        // Option souscriptible
                        $oSO = $oPO[$numPO]->getOption()->getSiteOptions();
                        for ($numSO = 0; $numSO < count($oSO); $numSO++) {
                            if ($oSO[$numSO]->getOption()->getId() === $optionId && $oSO[$numSO]->getActif() === true
                                && $oSO[$numSO]->getDateDebut() <= $dateDuJour
                                && ($oSO[$numSO]->getDateFin() > $dateDuJour || !$oSO[$numSO]->getDateFin())
                            ) {
                                return true; // Option souscrite
                            }
                        }
                    }
                }
            }
            return false; // Option non souscriptible ou non souscrite ou option inactive

        } else {
            return true; // Elément non lié à une option
        }
    }
}
