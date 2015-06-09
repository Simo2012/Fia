<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;

class QuestionStatutRepository extends EntityRepository
{
    /**
     * Récupère le statut correspondant à l'identifiant donné. Le résultat est en cache pendant 1 jour.
     *
     * @param integer $id Identifiant du statut
     *
     * @return QuestionStatut Instance de QuestionStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getStatut($id)
    {
        $qb = $this->createQueryBuilder('fs')
            ->where('fs.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()
            ->useQueryCache(true)->useResultCache(true)->setResultCacheLifetime(86400)
            ->getSingleResult();
    }

    /**
     * Récupère le statut "Désactivée". Le résultat est en cache pendant 1 jour.
     *
     * @return QuestionStatut Instance de QuestionStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function desactivee()
    {
        return $this->getStatut(0);
    }

    /**
     * Récupère le statut "Activée". Le résultat est en cache pendant 1 jour.
     *
     * @return QuestionStatut Instance de QuestionStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function activee()
    {
        return $this->getStatut(1);
    }

    /**
     * Récupère le statut "En attente de validation". Le résultat est en cache pendant 1 jour.
     *
     * @return QuestionStatut Instance de QuestionStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enAttenteValidation()
    {
        return $this->getStatut(2);
    }

    /**
     * Récupère le statut "Refusée". Le résultat est en cache pendant 1 jour.
     *
     * @return QuestionStatut Instance de QuestionStatut
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function refusee()
    {
        return $this->getStatut(3);
    }
}
