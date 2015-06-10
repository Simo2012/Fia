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
            ->andWhere($qb->expr()->eq('q.questionStatut', 1))
            ->setParameter('id', $questionnaireType->getId())
            ->orderBy('q.ordre', 'ASC')
            ->addOrderBy('q.page', 'ASC');
        
        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();
    }

    /**
     * Récupère l'ordre maximal des questions pour un type de questionnaire et un site.
     * Les questions personnalisées du site sont prises en compte.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     *
     * @return int L'ordre maximal trouvée. S'il n'y a encore aucune question, retourne 0.
     */
    public function maxOrdre(Site $site, QuestionnaireType $questionnaireType)
    {
        $qb = $this->createQueryBuilder('q');

        $qb->select('COALESCE(MAX(q.ordre), 0)')
            ->where('q.questionnaireType = :qtid')
            ->setParameter('qtid', $questionnaireType->getId())
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('q.site'),
                    $qb->expr()->eq('q.site', $site->getId())
                )
            );

        return $qb->getQuery()->getSingleScalarResult();
    }
}
