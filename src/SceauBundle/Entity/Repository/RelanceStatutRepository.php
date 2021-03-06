<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use SceauBundle\Cache\Cache;
use SceauBundle\Entity\RelanceStatut;

class RelanceStatutRepository extends EntityRepository
{
    /**
     * Récupère le statut correspondant à l'identifiant donné. Le résultat est en cache pendant 1 jour.
     *
     * @param integer $id Identifiant du statut
     *
     * @return RelanceStatut Instance de RelanceStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getStatut($id)
    {
        $qb = $this->createQueryBuilder('rs')
            ->where('rs.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J, "relanceStatut_$id")->getSingleResult();
    }

    /**
     * Récupère le statut "en attente de validation". Le résultat est en cache pendant 1 jour.
     *
     * @return RelanceStatut Instance de RelanceStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enAttenteDeValidation()
    {
        return $this->getStatut(RelanceStatut::EN_ATTENTE_DE_VALIDATION);
    }

    /**
     * Récupère le statut "validée". Le résultat est en cache pendant 1 jour.
     *
     * @return RelanceStatut Instance de RelanceStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validee()
    {
        return $this->getStatut(RelanceStatut::VALIDEE);
    }
}
