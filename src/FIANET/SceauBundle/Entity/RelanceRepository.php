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
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.site = :sid')
            ->setParameter('sid', $site->getId())
            ->andWhere('r.questionnaireType = :qtid')
            ->setParameter('qtid', $questionnaireType->getId())
            ->andWhere('r.langue = :lid')
            ->setParameter('lid', $langue->getId())
            ->andWhere('r.relanceStatut = 1')
            ->orderBy('r.date', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
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
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.site = :sid')
            ->setParameter('sid', $site->getId())
            ->andWhere('r.questionnaireType = :qtid')
            ->setParameter('qtid', $questionnaireType->getId())
            ->andWhere('r.langue = :lid')
            ->setParameter('lid', $langue->getId())
            ->andWhere('r.relanceStatut = 0');

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }
}
