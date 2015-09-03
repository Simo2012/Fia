<?php

namespace FIANET\SceauBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class QuestionRepository extends EntityRepository
{
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
            ->innerJoin('q.questionnaireTypes', 'questionnaireTypes', 'WITH', 'questionnaireTypes.id = :qtid')
            ->setParameter('qtid', $questionnaireType->getId())
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('q.site'),
                    $qb->expr()->eq('q.site', $site->getId())
                )
            );

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Récupère le nombre de questions personnalisées d'un site sur une période.
     * Les questions désactivées ou refusées ne sont pas prises en compte.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param DateTime $dateDebut Date de début de la période
     * @param DateTime $dateFin Date de fin de la période
     *
     * @return int Le nombre de questions
     */
    public function nbQuestionPersoPourUnePeriode(
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin
    ) {
        $qb = $this->createQueryBuilder('q');

        $qb->select('COUNT(q.id)')
            ->innerJoin('q.questionnaireTypes', 'questionnaireTypes', 'WITH', 'questionnaireTypes.id = :qtid')
            ->setParameter('qtid', $questionnaireType->getId())
            ->andWhere($qb->expr()->eq('q.site', $site->getId()))
            ->andWhere('q.dateDebut <= :dateFin')
            ->setParameter('dateDebut', $dateDebut)
            ->andWhere('q.dateFin >= :dateDebut')
            ->setParameter('dateFin', $dateFin)
            ->andWhere(
                $qb->expr()->in(
                    'q.questionStatut',
                    array(QuestionStatut::ACTIVEE, QuestionStatut::EN_ATTENTE_DE_VALIDATION)
                )
            );

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Récupère les questions personnalisées d'un site en attente de validation.
     * Elle sont triées par date de début croissante.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     *
     * @return Question[]
     */
    public function questionsPersosEnAttenteDeValidation(Site $site, QuestionnaireType $questionnaireType)
    {
        $qb = $this->createQueryBuilder('q');

        $qb->innerJoin('q.reponses', 'r')
            ->addSelect('r')
            ->innerJoin('q.questionType', 'qt')
            ->addSelect('qt')
            ->innerJoin('q.questionnaireTypes', 'questionnaireTypes', 'WITH', 'questionnaireTypes.id = :qtid')
            ->setParameter('qtid', $questionnaireType->getId())
            ->andWhere($qb->expr()->eq('q.site', $site->getId()))
            ->andWhere($qb->expr()->eq('q.questionStatut', QuestionStatut::EN_ATTENTE_DE_VALIDATION))
            ->orderBy('q.dateDebut', 'ASC')
            ->addOrderBy('r.ordre', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
