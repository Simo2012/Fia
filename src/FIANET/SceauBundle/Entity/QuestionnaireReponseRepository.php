<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;

class QuestionnaireReponseRepository extends EntityRepository
{
    /**
     * Permet de savoir de droits de réponse actifs possède une réponse à un questionnaire
     * 
     * @param QuestionnaireReponse $questionnaireReponse Instance de Site
     *
     * @return int Le nombre de droits de réponse actifs trouvés
     */
    public function nbDroitDeReponseActif(QuestionnaireReponse $questionnaireReponse)
    {
        $qb = $this->createQueryBuilder('qr');
        
        $qb->select('COUNT(dr.id)')
                ->innerJoin('qr.droitDeReponses', 'dr')
                ->where('qr.id=:id')
                ->setParameter('id', $questionnaireReponse->getId()) 
                ->andWhere($qb->expr()->eq('dr.actif', 'true'));
        
        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getSingleScalarResult();
    }
    
    /**
     * Récupère l'ensemble des réponses apportées à une question
     *
     * @param Question $question Instance de Question
     * @param Questionnaire $questionnaire Instance de Questionnaire
     *
     * @return QuestionnaireReponse[]|null instance de questionnaireReponse ou null si pas de réponse répondue trouvée
     */
    public function getAllReponsesRepondues(Question $question, Questionnaire $questionnaire)
    {
        $qb = $this->createQueryBuilder('qr');

        $qb->where('qr.question = :question')
            ->setParameter('question', $question)
            ->andWhere('qr.questionnaire = :questionnaire')
            ->setParameter('questionnaire', $questionnaire);
        
        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();
    }
    
}
