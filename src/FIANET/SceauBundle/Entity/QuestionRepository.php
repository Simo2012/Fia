<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;


class QuestionRepository extends EntityRepository
{
    /**
     * Récupère l'ensemble des questions d'un questionnaireType de manière ordonnée
     *
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     *
     * @return Question[]
     */
    public function getAllQuestionsOrdered(QuestionnaireType $questionnaireType)
    {
        /* ToDo : à revoir pour gestion : sous-questions, questions cachées, questions personnalisées, langues, etc. */
        
        $qb = $this->createQueryBuilder('q');

        $qb->where('q.questionnaireType=:id')
            ->andWhere($qb->expr()->eq('q.actif', 'true'))
            ->setParameter('id', $questionnaireType->getId())
            ->orderBy('q.ordre', 'ASC')
            ->addOrderBy('q.page', 'ASC');
        
        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();
    }
      
}
