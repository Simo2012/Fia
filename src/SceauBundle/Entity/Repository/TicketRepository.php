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
        	$qb->where($qb->expr()->eq('t.type.id', $params['type']));
        }

        if (isset($params['etat']) && $params['etat'] != '') {
        	$qb->where($qb->expr()->eq('t.etat', $params['etat']));
        }

        return $qb->getQuery()->getResult();
    }
}
