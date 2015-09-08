<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use SceauBundle\Cache\Cache;

class QuestionTypeRepository extends EntityRepository
{
    /**
     * Retourne un QueryBuilder qui permet de récupérer la liste des types de questions qui peuvent être utilisés
     * pour créer une question personnalisée.
     *
     * @return QueryBuilder Instance de QueryBuilder
     */
    public function typesPersonnalisablesQueryBuilder()
    {
        $qb = $this->createQueryBuilder('qt')
            ->where('qt.personnalisable = true');

        $qb->setCacheable(true)
            ->setLifetime(Cache::LIFETIME_1J);

        return $qb;
    }
}
