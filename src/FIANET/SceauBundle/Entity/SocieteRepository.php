<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use DateTime;

class SocieteRepository extends EntityRepository
{
    /**
     * Récupère l'ensemble des sites d'une société et l'ensemble des types de questionnaires utilisés par ces sites.
     *
     * @param integer $id Identifiant de la société
     *
     * @return Societe Instance de Societe
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function infosSocieteEtSitesLies($id)
    {
        $qb = $this->createQueryBuilder('so');

        $qb->addSelect('si')
            ->addSelect('siqp')
            ->addSelect('qt')
            ->join('so.sites', 'si')
            ->join('si.questionnairePersonnalisations', 'siqp')
            ->join('siqp.questionnaireType', 'qt')
            ->where('so.id = :id')
            ->setParameter('id', $id)
            ->andwhere('siqp.dateDebut < :dateDuJour')
            ->andWhere('siqp.dateFin IS NULL OR siqp.dateFin > :dateDuJour')
            ->setParameter('dateDuJour', new DateTime())
            ->orderBy('si.nom', 'ASC');

        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getSingleResult();
    }
}
