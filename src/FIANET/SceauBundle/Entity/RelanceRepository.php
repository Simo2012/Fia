<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RelanceRepository extends EntityRepository
{
    /**
     * Retourne l'éventuelle et dernière relance validée pour un site, un type de questionnaire et une langue.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param Langue $langue Instance de Langue
     *
     * @return Relance|null Instance de Relance ou null si elle n'existe pas
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function relanceValidee(Site $site, QuestionnaireType $questionnaireType, Langue $langue)
    {
        $qb = $this->createQueryBuilder('r');

        $qb->andWhere('r.site = :sid')
            ->setParameter('sid', $site->getId())
            ->andWhere('r.questionnaireType = :qtid')
            ->setParameter('qtid', $questionnaireType->getId())
            ->andWhere('r.langue = :lid')
            ->setParameter('lid', $langue->getId())
            ->andWhere($qb->expr()->eq('r.relanceStatut', RelanceStatut::VALIDEE))
            ->orderBy('r.date', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Retourne l'éventuelle relance en attente de validation pour pour un site, un type de questionnaire et une langue.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param Langue $langue Instance de Langue
     *
     * @return Relance|null Instance de Relance ou null si elle n'existe pas
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function relanceEnAttenteValidation(Site $site, QuestionnaireType $questionnaireType, Langue $langue)
    {
        $qb = $this->createQueryBuilder('r');

        $qb->andWhere('r.site = :sid')
            ->setParameter('sid', $site->getId())
            ->andWhere('r.questionnaireType = :qtid')
            ->setParameter('qtid', $questionnaireType->getId())
            ->andWhere('r.langue = :lid')
            ->setParameter('lid', $langue->getId())
            ->andWhere($qb->expr()->eq('r.relanceStatut', RelanceStatut::EN_ATTENTE_DE_VALIDATION));

        return $qb->getQuery()->getOneOrNullResult();
    }
}
