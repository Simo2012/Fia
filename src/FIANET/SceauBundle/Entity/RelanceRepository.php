<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RelanceRepository extends EntityRepository
{
    /**
     * Retourne la relance validée pour un site et pour une langue.
     *
     * @param Site $site Instance de Site
     * @param Langue $langue Instance de Langue
     *
     * @return Relance|null Instance de Relance ou null si elle n'existe pas
     */
    public function relanceValidee(Site $site, $langue)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.site = :sid')
            ->setParameter('sid', $site->getId())
            ->andWhere('r.langue = :lid')
            ->setParameter('lid', $langue->getId())
            ->andWhere('r.relanceStatut = 1');

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }
}
