<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use DateInterval;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use FIANET\SceauBundle\Entity\Flux;
use FIANET\SceauBundle\Entity\QuestionnairePersonnalisation;
use FIANET\SceauBundle\Entity\Site;
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
     * Vérifie que le crypt est correct.
     *
     * @param Site $site Instance de Site
     * @param string $xml XML du flux
     *
     * @return bool Si valide retourne true sinon false
     */
    private function verifierCrypt(Site $site, $xml)
    {
        $cryptAttendu = $this->gestionFlux->getCrypt(
            $site->getClePriveeSceau(),
            $xml->infocommande->refid,
            $xml->infocommande->ip['timestamp'],
            $xml->utilisateur->email
        );

        if ($xml->crypt == $cryptAttendu) {
            return true;

        } else {
            $this->context->addViolation('constraints.flux_crypt_incorrect');
            return false;
        }
    }

    /**
     * Vérifie que le l'identifiant de site donné est cohérent par rapport au site.
     *
     * @param Site $site Instance de Site
     * @param string $xml XML du flux
     *
     * @return bool Si valide retourne true sinon false
     */
    private function verifierSiteId(Site $site, $xml)
    {
        if ($xml->infocommande->siteid == $site->getId()) {
            return true;

        } else {
            $this->context->addViolation('constraints.flux_siteid_incorrect');
            return false;
        }
    }

    /**
     * Vérifie si la date de commande envoyée dans le flux est valide.
     *
     * @param string $xml XML du flux
     *
     * @return bool Si valide retourne true sinon false
     */
    private function verifierDateCommande($xml)
    {
        try {
            $dateCommande = new DateTime($xml->infocommande->ip['timestamp']); // Format : "YYYY-MM-DD H:i:s"
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
     * @param string $xml XML du flux
     *
     * @return bool Si valide retourne true sinon false
     */
    private function verifierDateUtilisation($xml)
    {
        try {
            $dateUtilisation = new DateTime($xml->infocommande->dateutilisation);  //format "YYYY-MM-DD"
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
     * Vérifie si le type de questionnaire donné dans le flux est bien disponible pour le site.
     *
     * @param QuestionnairePersonnalisation[] $questionnairePersos Tableau contenant les types de questionnaire du site
     * @param string $xml XML du flux
     *
     * @return bool Si valide retourne true sinon false
     */
    private function verifierQuestionnaire($questionnairePersos, $xml)
    {
        for ($i = 0; $i < count($questionnairePersos); $i++) {
            if ($questionnairePersos[$i]->getQuestionnaireType()->getId() == $xml->questionnaire) {
                return true;
            }
        }

        $this->context->addViolation('constraints.flux_questionnaire_incorrect');
        return false;
    }

    /**
     * Vérifie le contenu du XML d'un flux. Si le flux n'est pas valide, la première erreur rencontrée est renvoyée.
     * Dans l'ordre, on teste :
     * - Format XML
     * - Crypt correct
     * - Identifiant du site fourni en POST à la réception et celui contenu dans le XML sont cohérents
     * - Type de questionnaire autorisé à être utilisé par le site
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
            $site = $flux->getSite();

            if ($this->verifierCrypt($site, $xml)) {
                if ($this->verifierSiteId($site, $xml)) {
                    if (!$xml->questionnaire
                        || $this->verifierQuestionnaire($site->getQuestionnairePersonnalisations(), $xml)) {
                        if ($this->verifierDateCommande($xml)) {
                            if ($xml->infocommande->dateutilisation) {
                                $this->verifierDateUtilisation($xml);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            /* Normalement ce cas n'arrive pas : le format du XML est vérifié à l'étape de réception */
            $this->context->addViolation('constraints.erreur_interne');
        }
    }
}
