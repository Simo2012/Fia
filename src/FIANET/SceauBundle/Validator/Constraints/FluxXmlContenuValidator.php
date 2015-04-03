<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Exception;
use FIANET\SceauBundle\Entity\Flux;
use FIANET\SceauBundle\Service\GestionFlux;
use SimpleXMLElement;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FluxXmlContenuValidator extends ConstraintValidator
{
    private $em;
    private $gestionFlux;

    public function __construct(EntityManager $em, GestionFlux $gestionFluxon)
    {
        $this->em = $em;
        $this->gestionFlux = $gestionFluxon;
    }

    /**
     * Vérifie le contenu du XML d'un flux.
     * Si le flux n'est pas valide, la première erreur rencontrée est renvoyée.
     *
     * @param Flux $flux Instance de Flux
     *
     * @param Constraint $constraint Instance de FluxXmlContenu
     */
    public function validate($flux, Constraint $constraint)
    {
        libxml_use_internal_errors(true); // Désactivation de l'affichage des messages d'erreur sur la sortie standard

        try {
            $xml = new SimpleXMLElement($flux->getXml());

            /* Vérification du crypt */
            $cryptAttendu = $this->gestionFlux->getCrypt(
                $flux->getSite()->getClePriveeSceau(),
                $xml->infocommande->refid,
                $xml->infocommande->ip['timestamp'],
                $xml->utilisateur->email
            );

            // TODO vérifier comparaison des crypts
            var_dump($xml->crypt);
            var_dump($cryptAttendu);

            if ($xml->crypt == $cryptAttendu) {



            } else {

                $flux->setFluxStatut($this->em->getRepository('FIANETSceauBundle:FluxStatut')->find(4));
                $this->em->flush();

                $this->buildViolation('Crypt invalide')->addViolation();
            }

        } catch (Exception $e) {
            /* Normalement ce cas n'arrrive pas : le format est vérifié à l'étape précédente */
            $this->buildViolation('constraints.erreur_interne')->addViolation();
        }
    }
}
