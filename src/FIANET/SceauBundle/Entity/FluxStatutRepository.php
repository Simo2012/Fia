<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use FIANET\SceauBundle\Cache\Cache;

class FluxStatutRepository extends EntityRepository
{
    /**
     * Récupère le statut correspondant à l'identifiant donné. Le résultat est en cache pendant 1 jour.
     *
     * @param integer $id Identifiant du statut
     *
     * @return FluxStatut Instance de FluxStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getStatut($id)
    {
        $qb = $this->createQueryBuilder('fs')
            ->where('fs.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J, "fluxStatut_$id")->getSingleResult();
    }

    /**
     * Récupère le statut "A traiter". Le résultat est en cache pendant 1 jour.
     *
     * @return FluxStatut Instance de FluxStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function aTraiter()
    {
        return $this->getStatut(FluxStatut::FLUX_A_TRAITER);
    }

    /**
     * Récupère le statut "En cours de traitement". Le résultat est en cache pendant 1 jour.
     *
     * @return FluxStatut Instance de FluxStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enCoursDeTraitement()
    {
        return $this->getStatut(FluxStatut::FLUX_EN_COURS_DE_TRAITEMENT);
    }

    /**
     * Récupère le statut "Traité et valide". Le résultat est en cache pendant 1 jour.
     *
     * @return FluxStatut Instance de FluxStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function traiteEtValide()
    {
        return $this->getStatut(FluxStatut::FLUX_VALIDE);
    }

    /**
     * Récupère le statut "Traité et invalide". Le résultat est en cache pendant 1 jour.
     *
     * @return FluxStatut Instance de FluxStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function traiteEtInvalide()
    {
        return $this->getStatut(FluxStatut::FLUX_INVALIDE);
    }
}
