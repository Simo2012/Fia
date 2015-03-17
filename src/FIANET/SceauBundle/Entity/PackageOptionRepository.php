<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use FIANET\SceauBundle\Entity\Site;
use FIANET\SceauBundle\Entity\Option;

class PackageOptionRepository extends EntityRepository
{
    /**
     * Permet de savoir si l'option en paramètre est une option de base
     *
     * @param Site $site Instance de Site
     * @param Option $option Instance de Option
     *
     * @return PackageOption[]
     */
    public function optionDeBase(Site $site, Option $option)
    {
        $qb = $this->createQueryBuilder('po');
        
        $qb->where('po.package=:package')
            ->setParameter('package', $site->getPackage()->getId())
            ->andWhere($qb->expr()->eq('po.option', $option->getId()))
            ->andWhere($qb->expr()->eq('po.optionType', 1));

        return $qb->getQuery()->getResult();
    }
    
    /**
     * Permet de savoir si l'option en paramètre est une option soucriptible
     *
     * @param Site $site Instance de Site
     * @param Option $option Instance de Option
     *
     * @return PackageOption[]
     */
    public function optionSouscriptible(Site $site, Option $option)
    {
        $qb = $this->createQueryBuilder('po');
        
        $qb->addSelect()
            ->where('po.package=:package')
            ->setParameter('package', $site->getPackage()->getId())
            ->andWhere($qb->expr()->eq('po.option', $option->getId()))
            ->andWhere($qb->expr()->eq('po.optionType', 2));

        return $qb->getQuery()->getResult();
    }
    
}
