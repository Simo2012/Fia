<?php

namespace SceauBundle\Service;

use DateInterval;
use Doctrine\ORM\EntityManager;
use SceauBundle\Entity\Question;
use SceauBundle\Entity\QuestionType;

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
        $ordreMax = $this->em->getRepository('SceauBundle:Question')
            ->maxOrdre($question->getSite(), $question->getQuestionnaireTypes()[0]);
        $question->setOrdre($ordreMax + 10);
        $question->setLibelleCourt($question->getLibelle());
        $question->setCache(false);
        $question->setQuestionStatut(
            $this->em->getRepository('SceauBundle:QuestionStatut')->enAttenteValidation()
        );
        $question->setDateFin($question->getDateFin()->add(new DateInterval('P1D')));
        $question->setPage(1); // TODO : à revoir si on conserve cette info

        $ordre = 0;
        foreach ($question->getReponses() as $reponse) {
            $reponse->setLibelleCourt($reponse->getLibelle());
            $reponse->setOrdre($ordre);
            $reponse->setReponseStatut(
                $this->em->getRepository('SceauBundle:ReponseStatut')->activee()
            );

            if ($question->getQuestionType()->getId() != QuestionType::CHOIX_MULTIPLE) {
                $reponse->setPrecision(false);
            }

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
