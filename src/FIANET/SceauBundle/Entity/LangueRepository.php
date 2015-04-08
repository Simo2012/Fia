<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LangueRepository extends EntityRepository
{

    /**
     * Récupère la langue à utiliser par défaut. Le code de la langue par défaut doit être transmise à la méthode.
     * Ce code est stocké dans les paramètres.
     *
     * @param string $code Code ISO de la langue (ex : fr)
     *
     * @return mixed Instance de Langue
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLangueParDefaut($code)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.code=:code')
            ->setParameter('code', $code);

        return $qb->getQuery()
            ->useQueryCache(true)->useResultCache(true)->setResultCacheLifetime(86400)
            ->getSingleResult();
    }
}
