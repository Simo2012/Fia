<?php
namespace FIANET\SceauBundle\Service;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use FIANET\SceauBundle\Entity\Commande;
use FIANET\SceauBundle\Entity\Flux;
use FIANET\SceauBundle\Entity\Questionnaire;
use FIANET\SceauBundle\Entity\QuestionnaireType;
use FIANET\SceauBundle\Entity\QuestionnairePersonnalisation;
use SimpleXMLElement;

class GestionQuestionnaire
{
    private $em;
    private $codeLangueParDefaut;
    private $delaiEnvoiDateUtilisation;

    public function __construct(EntityManager $em, $codeLangueParDefaut, $delaiEnvoiDateUtilisation)
    {
        $this->em = $em;
        $this->codeLangueParDefaut = $codeLangueParDefaut;
        $this->delaiEnvoiDateUtilisation = $delaiEnvoiDateUtilisation;
    }

    /**
     * Retourne un objet DateInterval en fonction du délai d'envoi passé en argument.
     *
     * @param integer $nbJours Nombre de jours du délai d'envoi
     *
     * @return DateInterval Instance de DateInterval
     */
    private function intervalleDelaiEnvoi($nbJours)
    {
        return new DateInterval('P' . $nbJours . 'D');
    }

    /**
     * Retourne la date d'envoi du questionnaire selon les critères suivants :
     * 1) S'il y a une date d'utilisation, on envoie le questionnaire X jours après cette date (paramètre yml).
     * 2) Si le site a personnalisé le délai d'envoi : on ajoute ce délai à la date du jour.
     * 3) Dans tous les autres cas : on ajoute le délai par défaut en fonction du type de questionnaire.
     *
     * @param QuestionnairePersonnalisation $questionnairePersonnalisation Instance de QuestionnairePersonnalisation
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param Commande $commande Instance de Commande
     *
     * @return DateTime Retourne la date d'envoi du questionnaire
     */
    private function fixerDateEnvoi(
        QuestionnairePersonnalisation $questionnairePersonnalisation,
        QuestionnaireType $questionnaireType,
        Commande $commande
    ) {
        $dateEnvoi = new DateTime();

        if ($commande->getDateUtilisation()) {
            $dateEnvoi->add(new DateInterval($this->delaiEnvoiDateUtilisation));

        } elseif ($questionnairePersonnalisation->getDelaiEnvoi()) {
            $dateEnvoi->add($this->intervalleDelaiEnvoi($questionnairePersonnalisation->getDelaiEnvoi()->getNbJours()));

        } else {
            $dateEnvoi->add($this->intervalleDelaiEnvoi($questionnaireType->getDelaiEnvoi()->getNbJours()));
        }

        return $dateEnvoi;
    }

    /**
     * Crée une instance de Commande à partir du flux XML envoyé par le marchand.
     *
     * @param string $xml XML de la commande
     *
     * @return Commande Instance de Commande
     */
    private function creerCommandeLieeAuFlux($xml)
    {
        $commande = new Commande();
        $commande->setEmail($xml->utilisateur->email->__toString());
        if ($xml->utilisateur->prenom->__toString() != '') {
            $commande->setPrenom($xml->utilisateur->prenom->__toString());
        }
        $commande->setNom($xml->utilisateur->nom->__toString());
        $commande->setDate(new DateTime($xml->infocommande->ip['timestamp']));
        $commande->setReference($xml->infocommande->refid->__toString());
        $commande->setMontant((float)$xml->infocommande->montant->__toString());

        // TODO : Vérifie que le type de questionnaire autorise à utiliser cette balise
        if ($xml->infocommande->dateutilisation) {
            $commande->setDateUtilisation(new DateTime($xml->infocommande->dateutilisation));
        }

        $langue = null;
        if (!$xml->infocommande->langue) {
            $langue = $this->em->getRepository('FIANETSceauBundle:Langue')
                ->langueViaCode($this->codeLangueParDefaut);
        } else {
            $langue = $this->em->getRepository('FIANETSceauBundle:Langue')
                ->langueViaCode($xml->infocommande->langue->__toString());
            if (!$langue) {
                $langue = $this->em->getRepository('FIANETSceauBundle:Langue')
                    ->langueViaCode($this->codeLangueParDefaut);
            }
        }
        $commande->setLangue($langue);

        return $commande;
    }

    /**
     * Génère un questionnaire à partir d'un flux.
     *
     * @param Flux $flux Instance de flux
     * @param SimpleXMLElement $xml Instance de SimpleXMLElement contenant l'XML de la commande
     */
    public function genererQuestionnaireViaFlux(Flux $flux, SimpleXMLElement $xml)
    {
        $questionnairePersonnalisation = null;
        $questionnaireType = null;
        $questionnairePersonnalisations = $flux->getSite()->getQuestionnairePersonnalisations();

        if (!$xml->questionnaire) {
            $i = 0;
            while (!$questionnaireType) {
                if ($questionnairePersonnalisations[$i]->getPrincipal() === true) {
                    $questionnairePersonnalisation = $questionnairePersonnalisations[$i];
                    $questionnaireType = $questionnairePersonnalisations[$i]->getQuestionnaireType();
                }
                $i++;
            }
        } else {
            /* TODO : si une balise questionnaire est présente, il faut vérifier que le site puisse utiliser ce
            questionnaire. Si oui, le QuestionnaireType sera égal au questionnaire indiqué. */
        }

        $commande = $this->creerCommandeLieeAuFlux($xml);
        $commande->setSite($flux->getSite());
        $commande->setQuestionnaireType($questionnaireType);
        if ($flux) {
            $commande->setFlux($flux);
        }

        $questionnaire = new Questionnaire();
        $questionnaire->setCommande($commande);
        $questionnaire->setSite($flux->getSite());
        $questionnaire->setLangue($commande->getLangue());
        $questionnaire->setQuestionnaireType($questionnaireType);
        $questionnaire->setEmail($commande->getEmail());
        $questionnaire->setDateInsertion(new DateTime());
        $questionnaire->setDatePrevEnvoi(
            $this->fixerDateEnvoi($questionnairePersonnalisation, $questionnaireType, $commande)
        );
        $questionnaire->setActif(true);

        $this->em->persist($questionnaire);
        $this->em->flush();
    }
}
