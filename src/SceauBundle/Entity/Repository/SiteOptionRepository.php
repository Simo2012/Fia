<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use SceauBundle\Entity\Option;
use SceauBundle\Entity\Site;
use SceauBundle\Entity\SiteOption;

class SiteOptionRepository extends EntityRepository
{
    /**
     * Permet de savoir si l'option en paramètre est souscrite pour le site
     *
     * @param Site $site Instance de Site
     * @param Option $option Instance de Option
     *
     * @return SiteOption[]
     */
    public function optionSouscrite(Site $site, Option $option)
    {
        $qb = $this->createQueryBuilder('so');

        $qb->where('so.site=:site')
            ->setParameter('site', $site->getId())
            ->setParameter('dateDuJour', new \DateTime(date('Y-m-d')))
            ->andWhere($qb->expr()->eq('so.option', $option->getId()))
            ->andWhere($qb->expr()->eq('so.actif', 'true'))
            ->andWhere($qb->expr()->lte('so.dateDebut', ':dateDuJour'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('so.dateFin'),
                    $qb->expr()->gt('so.dateFin', ':dateDuJour')
                )
            );

        return $qb->getQuery()->getResult();
    }
}
