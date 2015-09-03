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
                $qb->expr()->eq('fs.id', FluxStatut::FLUX_A_TRAITER),
                $qb->expr()->eq('fs.id', FluxStatut::FLUX_EN_COURS_DE_TRAITEMENT) // TODO à enlever : il faut faire un rollback dans la commande en "A traiter"
            ))
            ->andWhere('qp.principal=true') // TODO : mettre en place une pagination pour pouvoir supprimer cette ligne
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('qp.dateFin'),
                $qb->expr()->gt('qp.dateFin', ':today')
            ))
            ->setParameter('today', $dateActuelle->format('Y-m-d'))
            ->orderBy('f.id', 'ASC')
            ->setMaxResults($nbMaxFlux);

        return $qb->getQuery()->getResult();
    }
}
