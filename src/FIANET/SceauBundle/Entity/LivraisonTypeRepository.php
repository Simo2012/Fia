<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use FIANET\SceauBundle\Cache\Cache;

class LivraisonTypeRepository extends EntityRepository
{
    /**
     * Récupère le type de livraison correspondant à l'identifiant donné. Le résultat est en cache pendant 1 jour.
     *
     * @param integer $id Identifiant du type de livraison
     *
     * @return LivraisonType Instance de LivraisonType
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getLivraisonType($id)
    {
        $qb = $this->createQueryBuilder('lt')
            ->where('lt.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J)->getSingleResult();
    }

    /**
     * Récupère le type de livraison "Aucun". Le résultat est en cache pendant 1 jour.
     *
     * @return LivraisonType Instance de LivraisonType
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function aucun()
    {
        return $this->getLivraisonType(LivraisonType::AUCUN);
    }

    /**
     * Retourne un QueryBuilder qui permet de récupérer les types de livraison qui sont destinés à être affichés dans
     * une liste déroulante. Le résultat est en cache pendant 1 jour.
     *
     * @return QueryBuilder
     */
    public function menuDeroulant()
    {
        $qb = $this->createQueryBuilder('lt')
            ->where('lt.affichage = true');

        $qb->setCacheable(true)
            ->setLifetime(Cache::LIFETIME_1J);

        return $qb;
    }
}
