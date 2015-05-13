<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RelanceRepository extends EntityRepository
{
    /**
     * Retourne la relance validée pour un site, un type de questionnaire et une langue.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param Langue $langue Instance de Langue
     * @param bool $auto Si on veut ajouter une restriction sur l'automatisation de la relance. Par défaut, vaut false
     *
     * @return Relance|null Instance de Relance ou null si elle n'existe pas
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function relanceValidee(Site $site, QuestionnaireType $questionnaireType, $langue, $auto = false)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.site = :sid')
            ->setParameter('sid', $site->getId())
            ->andWhere('r.questionnaireType = :qtid')
            ->setParameter('qtid', $questionnaireType->getId())
            ->andWhere('r.langue = :lid')
            ->setParameter('lid', $langue->getId())
            ->andWhere('r.relanceStatut = 1');

        if ($auto) {
            $qb->andWhere('r.auto = true');
        }

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }
}
