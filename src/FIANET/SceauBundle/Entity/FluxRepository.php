<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;

class FluxRepository extends EntityRepository
{
    /**
     * Retourne X flux qui n'ont pas encore été traités par le script de validation des flux.
     *
     * @param integer $nbMaxFlux Nombre maximum de flux à récupérer
     *
     * @return Flux[]
     */
    public function fluxNonTraites($nbMaxFlux)
    {
        $qb = $this->createQueryBuilder('f');

        $qb->addSelect('s')
            ->innerJoin('f.fluxStatut', 'fs')
            ->innerJoin('f.site', 's')
            ->where('fs.id = :id')
                ->setParameter('id', 1)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults($nbMaxFlux);

        return $qb->getQuery()->getResult();
    }
}
