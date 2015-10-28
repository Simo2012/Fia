<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TicketRepository extends EntityRepository
{

    public function getTicketsByParams($params)
    {
        $qb = $this->createQueryBuilder('t')
        	->where('t.id is not null');


        if (isset($params['type']) && $params['type'] != '') {
        	$qb->andWhere($qb->expr()->eq('t.type.id', $params['type']));
        }

        if (isset($params['etat']) && $params['etat'] != '') {
        	$qb->andWhere('t.etat = :etat')
        	 	->setParameter('etat', $params['etat']);
        }

        return $qb->getQuery()->getResult();
    }
}
