<?php

namespace SceauBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityRepository;
use SceauBundle\Cache\Cache;

class FluxRepository extends EntityRepository
{
    /**
     * Retourne X identifiants de flux à traiter. Les plus anciens flux sont pris en priorité.
     *
     * @param int $nbMaxFlux Nombre d'identifiants de flux à retourner
     *
     * @return array Tableau d'identifiants
     */
    public function listeIdFluxNonTraites($nbMaxFlux)
    {
        $qb = $this->createQueryBuilder('f');

        $qb->select('f.id')
            ->innerJoin('f.fluxStatut', 'fs')
            ->where($qb->expr()->eq('fs.id', FluxStatut::FLUX_A_TRAITER))
            ->orderBy('f.id', 'ASC')
            ->setMaxResults($nbMaxFlux);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Met à jour le statut des flux avec le statut "En cours de traitement". Les flux mis à jour sont ceux dont les
     * identifiants sont passés en argument.
     *
     * @param array $ids Tableau contenant les identifiants
     */
    public function updateEnCoursDeTraitement($ids)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->update($this->_entityName, 'f')
            ->set('f.fluxStatut', FluxStatut::FLUX_EN_COURS_DE_TRAITEMENT)
            ->where($qb->expr()->in('f.id', ':ids'))
            ->setParameter('ids', $ids);

        $qb->getQuery()->execute();
    }

    /**
     * Récupère les flux dont les identifiants sont passés en argument. La requête récupère également les relations avec
     * Site et QuestionnairePersonnalisation.
     *
     * @param array $ids Tableau contenant les identifiants
     *
     * @return Flux[]
     */
    public function listeFluxParId($ids)
    {
        $dateActuelle = new DateTime();

        $qb = $this->createQueryBuilder('f');

        $qb->addSelect('s')
            ->addSelect('qp')
            ->innerJoin('f.fluxStatut', 'fs')
            ->innerJoin('f.site', 's')
            ->innerJoin('s.questionnairePersonnalisations', 'qp')
            ->where($qb->expr()->in('f.id', ':ids'))
            ->setParameter('ids', $ids)
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('qp.dateFin'),
                $qb->expr()->gt('qp.dateFin', ':today')
            ))
            ->setParameter('today', $dateActuelle->format('Y-m-d'))
            ->orderBy('f.id', 'ASC');

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_15M)->getResult();
    }
}
