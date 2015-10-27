<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TicketRepository extends EntityRepository
{

    public function getTicketsByParams($params)
    {
    	var_dump('toto : ', $params);
        // $qb = $this->createQueryBuilder('t');

        // if ($params['type']) {
        // 	$qb->leftJoin('t.')
        // }

        // $qb->where('po.package=:package')
        //     ->setParameter('package', $site->getPackage()->getId())
        //     ->andWhere($qb->expr()->eq('po.option', $option->getId()))
        //     ->andWhere($qb->expr()->eq('po.optionType', 1));

        // return $qb->getQuery()->getResult();
    }
}
