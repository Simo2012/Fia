<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TicketReponseModeleRepository extends EntityRepository
{
    /**
     * Récupère les types des modeles de réponse aux tickets.
     */
    public function ticketReponseModeleTypes()
    {
        return $this->createQueryBuilder('t')
            //s->select('t.type')
            ->getQuery()
            ->getResult()
        ;
    }
}
