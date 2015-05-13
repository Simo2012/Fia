<?php

namespace FIANET\SceauBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityRepository;

class FluxRepository extends EntityRepository
{
    /**
     * Retourne X flux qui n'ont pas encore été traités par le script de validation des flux :
     * statut "A traiter" et "En cours de traitement".
     *
     * @param integer $nbMaxFlux Nombre maximum de flux à récupérer
     *
     * @return Flux[]
     */
    public function fluxNonTraites($nbMaxFlux)
    {
        $dateActuelle = new DateTime();

        $qb = $this->createQueryBuilder('f');

        $qb->addSelect('s')
            ->addSelect('qp')
            ->innerJoin('f.fluxStatut', 'fs')
            ->innerJoin('f.site', 's')
            ->innerJoin('s.questionnairePersonnalisations', 'qp')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('fs.id', ':a_traiter'),
                $qb->expr()->eq('fs.id', ':en_cours')
            ))
            ->setParameter('a_traiter', 1)
            ->setParameter('en_cours', 2)
            ->andWhere('qp.principal=true')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('qp.dateFin'),
                $qb->expr()->gt('qp.dateFin', ':today')
            ))
            ->setParameter('today', $dateActuelle->format('Y-m-d'))
            ->orderBy('f.id', 'ASC')
            ->setMaxResults($nbMaxFlux);

        return $qb->getQuery()->useQueryCache(true)->getResult();
    }
}
