<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use FIANET\SceauBundle\Entity\Flux;
use FIANET\SceauBundle\Service\GestionFlux;
use FIANET\SceauBundle\Service\GestionQuestionnaire;
use SimpleXMLElement;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FluxXmlContenuValidator extends ConstraintValidator
{
    private $em;
    private $gestionFlux;
    private $gestionQuestionnaire;
    private $delaiMaxCommande;

    public function __construct(
        EntityManager $em,
        GestionFlux $gestionFlux,
        GestionQuestionnaire $gestionQuestionnaire,
        $delaiMaxCommande
    ) {
        $this->em = $em;
        $this->gestionFlux = $gestionFlux;
        $this->gestionQuestionnaire = $gestionQuestionnaire;
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
                $this->buildViolation('constraints.flux_timestamp_futuriste')->addViolation();
                return false;

            } else {
                /* Date trop ancienne */
                $dateLimite = $dateActuelle;
                $dateLimite->sub(new DateInterval($this->delaiMaxCommande));

                if ($dateCommande < $dateLimite) {
                    $this->buildViolation('constraints.flux_timestamp_trop_ancien')->addViolation();
                    return false;

                } else {
                    return true;
                }
            }
        } catch (Exception $e) {
            /* Date invalide */
            $this->buildViolation('constraints.flux_timestamp_incorrect')->addViolation();
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
                $this->buildViolation('constraints.flux_dateutilisation_passe')->addViolation();
                return false;

            } else {
                return true;
            }
        } catch (Exception $e) {
            /* Date invalide */
            $this->buildViolation('constraints.flux_dateutilisation_incorrect')->addViolation();
            return false;
        }
    }

    /**
     * Vérifie le contenu du XML d'un flux.
     * Si le flux n'est pas valide, la première erreur rencontrée est renvoyée.
     * Si le flux est valide, une demande au service de génération de questionnaire est effectuée.
     *
     * @param Flux $flux Instance de Flux
     *
     * @param Constraint $constraint Instance de FluxXmlContenu
     *
     * @return bool true Si valide retourne true sinon false
     */
    public function validate($flux, Constraint $constraint)
    {
        libxml_use_internal_errors(true); // Désactivation de l'affichage des messages d'erreur sur la sortie standard

        try {
            $xml = new SimpleXMLElement($flux->getXml());

        } catch (Exception $e) {
            /* Normalement ce cas n'arrrive pas : le format est vérifié à l'étape précédente */
            $this->buildViolation('constraints.erreur_interne')->addViolation();

            return false;
        }

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
                /* L'identifiant du site fourni en POST et celui contenu dans le XML sont cohérents */

                if ($this->verifierValiditeDateCommande($xml->infocommande->ip['timestamp'])) {
                    /* La date de commande est valide */

                    if ($xml->infocommande->dateutilisation) {
                        if ($this->verifierValiditeDateUtilisation($xml->infocommande->dateutilisation->__toString())) {
                            /* Présence d'une date d'utilisation valide */

                            $this->gestionQuestionnaire->genererQuestionnaireViaFlux($flux, $xml);
                        } else {
                            return false;
                        }

                    } else {
                        $this->gestionQuestionnaire->genererQuestionnaireViaFlux($flux, $xml);
                    }
                } else {
                    return false;
                }
            } else {
                $this->buildViolation('constraints.flux_siteid_incorrect')->addViolation();

                return false;
            }
        } else {
            $this->buildViolation('constraints.flux_crypt_incorrect')->addViolation();

            return false;
        }

        return true;
    }
}
