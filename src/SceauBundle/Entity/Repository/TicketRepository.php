<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TicketRepository extends EntityRepository
{

    public function getTicketsByParams($params)
    {
        $qb = $this->createQueryBuilder('t');

        if (isset($params['type']) && $params['type'] != '') {
        	$qb->where($qb->expr()->eq('t.type', $params['type']));
        }

        if (isset($params['etat']) && $params['etat'] != '') {
        	$qb->where('t.etat = :etat')
        	 	->setParameter('etat', $params['etat']);
        }

        return $qb->getQuery()->getResult();
    }
}
