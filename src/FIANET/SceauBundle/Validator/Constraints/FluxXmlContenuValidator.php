<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use DateInterval;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
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
    private $delaiMaxCommande;

    public function __construct(ObjectManager $em, GestionFlux $gestionFlux, $delaiMaxCommande)
    {
        $this->em = $em;
        $this->gestionFlux = $gestionFlux;
        $this->delaiMaxCommande = $delaiMaxCommande;
    }

    /**
     * Vérifie si la date de commande envoyée dans le flux est valide.
     *
     * @param string $date La date de commande contenue dans le flux au format "YYYY-MM-DD H:i:s"
     *
     * @return bool Si valide retourne true sinon false
     */
    private function verifierValiditeDateCommande($date)
    {
        try {
            $dateCommande = new DateTime($date);
            $dateActuelle = new DateTime();

            if ($dateCommande > $dateActuelle) {
                /* Date futuriste */
                $this->context->addViolation('constraints.flux_timestamp_futuriste');
                return false;

            } else {
                /* Date trop ancienne */
                $dateLimite = $dateActuelle;
                $dateLimite->sub(new DateInterval($this->delaiMaxCommande));

                if ($dateCommande < $dateLimite) {
                    $this->context->addViolation('constraints.flux_timestamp_trop_ancien');
                    return false;

                } else {
                    return true;
                }
            }
        } catch (Exception $e) {
            /* Date invalide */
            $this->context->addViolation('constraints.flux_timestamp_incorrect');
            return false;
        }
    }

    /**
     * Vérifie si la date d'utilisation envoyée dans le flux est valide.
     *
     * @param string $date La date de commande contenue dans le flux au format "YYYY-MM-DD"
     *
     * @return bool Si valide retourne true sinon false
     */
    private function verifierValiditeDateUtilisation($date)
    {
        try {
            $dateUtilisation = new DateTime($date);
            $dateActuelle = new DateTime();
            $dateActuelle->setTime(0, 0, 0);

            if ($dateUtilisation < $dateActuelle) {
                /* Date dans le passé */
                $this->context->addViolation('constraints.flux_dateutilisation_passe');
                return false;

            } else {
                return true;
            }
        } catch (Exception $e) {
            /* Date invalide */
            $this->context->addViolation('constraints.flux_dateutilisation_incorrect');
            return false;
        }
    }

    /**
     * Vérifie le contenu du XML d'un flux. Si le flux n'est pas valide, la première erreur rencontrée est renvoyée.
     * Dans l'ordre, on teste :
     * - Format XML
     * - Crypt correct
     * - Identifiant du site fourni en POST à la réception et celui contenu dans le XML sont cohérents
     * - Validité date de commande
     * - Validité date d'utilisation
     *
     * @param Flux $flux Instance de Flux
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

            if ($xml->crypt == $cryptAttendu) {
                $site = $flux->getSite();

                if ($xml->infocommande->siteid == $site->getId()) {

                    if ($this->verifierValiditeDateCommande($xml->infocommande->ip['timestamp'])) {

                        if ($xml->infocommande->dateutilisation) {
                            $this->verifierValiditeDateUtilisation($xml->infocommande->dateutilisation->__toString());
                        }
                    }
                } else {
                    $this->context->addViolation('constraints.flux_siteid_incorrect');
                }

            } else {
                $this->context->addViolation('constraints.flux_crypt_incorrect');
            }

        } catch (Exception $e) {
            /* Normalement ce cas n'arrive pas : le format du XML est vérifié à l'étape de réception */
            $this->context->addViolation('constraints.erreur_interne');
        }
    }
}
