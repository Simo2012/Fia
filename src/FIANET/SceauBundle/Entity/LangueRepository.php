<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;

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

        return $qb->getQuery()
            ->useQueryCache(true)->useResultCache(true)->setResultCacheLifetime(86400)
            ->getSingleResult();
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

        return $qb->getQuery()
            ->useQueryCache(true)->useResultCache(true)->setResultCacheLifetime(86400)
            ->getSingleResult();
    }
}
