<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use SceauBundle\Cache\Cache;
use SceauBundle\Entity\QuestionStatut;

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

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J, "questionStatut_$id")->getSingleResult();
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
        return $this->getStatut(QuestionStatut::DESACTIVEE);
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
        return $this->getStatut(QuestionStatut::ACTIVEE);
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
        return $this->getStatut(QuestionStatut::EN_ATTENTE_DE_VALIDATION);
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
        return $this->getStatut(QuestionStatut::REFUSEE);
    }
}
