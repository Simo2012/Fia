<?php
namespace FIANET\SceauBundle\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use FIANET\SceauBundle\Entity\Commande;
use FIANET\SceauBundle\Entity\Flux;
use FIANET\SceauBundle\Entity\Questionnaire;
use SimpleXMLElement;

class GestionQuestionnaire
{
    private $em;
    private $codeLangueParDefaut;

    public function __construct(EntityManager $em, $codeLangueParDefaut)
    {
        $this->em = $em;
        $this->codeLangueParDefaut = $codeLangueParDefaut;
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
                ->getLangueParDefaut($this->codeLangueParDefaut);
        } else {
            $langue = $this->em->getRepository('FIANETSceauBundle:Langue')
                ->findOneByCode($xml->infocommande->langue->__toString());
            if (!$langue) {
                $langue = $this->em->getRepository('FIANETSceauBundle:Langue')
                    ->getLangueParDefaut($this->codeLangueParDefaut);
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
        $questionnaireType = null;
        $questionnairePersonnalisations = $flux->getSite()->getQuestionnairePersonnalisations();

        if (!$xml->questionnaire) {
            $i = 0;
            while (!$questionnaireType) {
                if ($questionnairePersonnalisations[$i]->getPrincipal() === true) {
                    $questionnaireType = $questionnairePersonnalisations[$i]->getQuestionnaireType();
                }
                $i++;
            }
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
        $questionnaire->setDatePrevEnvoi(new DateTime()); // TODO à gérer
        $questionnaire->setActif(true);

        $this->em->persist($questionnaire);
        $this->em->flush();
    }
}
