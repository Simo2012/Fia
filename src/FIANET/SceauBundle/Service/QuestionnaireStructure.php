<?php

namespace FIANET\SceauBundle\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use FIANET\SceauBundle\Entity\Langue;
use FIANET\SceauBundle\Entity\Question;
use FIANET\SceauBundle\Entity\QuestionnaireType;
use FIANET\SceauBundle\Entity\QuestionType;
use FIANET\SceauBundle\Entity\Relance;
use FIANET\SceauBundle\Entity\Site;

/**
 * Service qui permet de gérer les structures des types de questionnaire (création de questions/réponses, etc).
 */
class QuestionnaireStructure
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Méthode qui complète les données obligatoire pour une question personnalisées. En effet, on ne donne pas
     * complétement la main à l'utilisateur pour créer une question. Ceretaines valeurs sont fixés automatiquement.
     *
     * @param Question $question Instance de Question
     */
    private function completerDonneesQuestionPerso(Question $question)
    {
        $ordreMax = $this->em->getRepository('FIANETSceauBundle:Question')
            ->maxOrdre($question->getSite(), $question->getQuestionnaireType());
        $question->setOrdre($ordreMax + 10);
        $question->setLibelleCourt($question->getLibelle());
        $question->setCache(false);
        $question->setQuestionStatut(
            $this->em->getRepository('FIANETSceauBundle:QuestionStatut')->enAttenteValidation()
        );
        $question->setPage(1); // TODO : à revoir si on conserve cette info

        $ordre = 0;
        foreach ($question->getReponses() as $reponse) {
            $reponse->setLibelleCourt($reponse->getLibelle());
            $reponse->setOrdre($ordre);
            //$reponse->setPrecision(false); // TODO à revoir
            $reponse->setActif(true);

            $ordre += 10;
        }
    }

    /**
     * Ajoute une question personnalisée à un type de questionnaire pour un site.
     *
     * @param Question $question Instance de Question
     */
    public function ajouterQuestionPerso(Question $question)
    {
        $this->completerDonneesQuestionPerso($question);

        $this->em->persist($question);
        $this->em->flush();
    }
}
