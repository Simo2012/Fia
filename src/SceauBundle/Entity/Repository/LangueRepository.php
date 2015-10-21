<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use SceauBundle\Cache\Cache;

class LangueRepository extends EntityRepository
{
    /**
     * Récupère une langue via son identifiant. Le résultat est en cache pendant 1 jour.
     *
     * @param integer $id Identifiant de la langue
     *
     * @return Langue Instance de Langue
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function langueViaId($id)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J, "langue_$id")->getSingleResult();
    }

    /**
     * Récupère une langue via le code de la langue. Le résultat est en cache pendant 1 jour.
     *
     * @param string $code Code ISO de la langue (ex : fr)
     *
     * @return Langue Instance de Langue
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function langueViaCode($code)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.code = :code')
            ->setParameter('code', $code);

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J, "langue_$code")->getSingleResult();
    }

    /**
     * Retourne un QueryBuilder qui permet de récupérer les langues qui sont destinées à être affichées dans
     * une liste déroulante. Le résultat est en cache pendant 1 jour.
     *
     * @return QueryBuilder
     */
    public function menuDeroulant()
    {
        $qb = $this->createQueryBuilder('l');

        $qb->setCacheable(true)
            ->setLifetime(Cache::LIFETIME_1J);

        return $qb;
    }
}
