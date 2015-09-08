<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use SceauBundle\Cache\Cache;

class ReponseStatutRepository extends EntityRepository
{
    /**
     * Récupère le statut correspondant à l'identifiant donné. Le résultat est en cache pendant 1 jour.
     *
     * @param integer $id Identifiant du statut
     *
     * @return ReponseStatut Instance de ReponseStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getStatut($id)
    {
        $qb = $this->createQueryBuilder('rs')
            ->where('rs.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J, "reponseStatut_$id")->getSingleResult();
    }

    /**
     * Récupère le statut "Désactivée". Le résultat est en cache pendant 1 jour.
     *
     * @return ReponseStatut Instance de ReponseStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function desactivee()
    {
        return $this->getStatut(ReponseStatut::DESACTIVEE);
    }

    /**
     * Récupère le statut "Activée". Le résultat est en cache pendant 1 jour.
     *
     * @return ReponseStatut Instance de ReponseStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function activee()
    {
        return $this->getStatut(ReponseStatut::ACTIVEE);
    }
}
