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
     * @param Site $site Instance de Site
     * @param Question $question Instance de Question
     */
    private function completerDonneesQuestionPerso(Site $site, Question $question)
    {
        $question->setSite($site);
        $ordreMax = $this->em->getRepository('FIANETSceauBundle:Question')
            ->maxOrdre($site, $question->getQuestionnaireType());
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
            $reponse->setPrecision(false); // TODO à revoir
            $reponse->setActif(true);

            $ordre += 10;
        }
    }

    /**
     * Ajoute une question personnalisée à un type de questionnaire pour un site.
     *
     * @param Site $site Ins
     * @param Question $question Instance de Question
     */
    public function ajouterQuestionPerso(Site $site, Question $question)
    {
        $this->completerDonneesQuestionPerso($site, $question);

        $this->em->persist($question);
        $site->addQuestion($question);
        $this->em->flush();
    }
}
