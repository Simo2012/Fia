<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use SceauBundle\Cache\Cache;
use SceauBundle\Entity\Indice;

class IndiceRepository extends EntityRepository
{
    /**
     * Permet de récupérer une instance d'indice avec tout son paramètrage.
     *
     * @param int $id Identifiant de l'indice
     *
     * @return Indice Instance d'Indice
     */
    public function getIndice($id)
    {
        $qb = $this->createQueryBuilder('i');

        $qb->innerJoin('indiceType', 'it')
            ->addSelect('it')
            ->innerJoin('questionnaireType', 'qt')
            ->addSelect('qt')
            ->leftJoin('question', 'q')
            ->addSelect('q')
            ->leftJoin('reponse', 'r')
            ->addSelect('r')
            ->leftJoin('indicesSecondaires', 'is')
            ->addSelect('is')
            ->where('i.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J, "indice_$id")->getResult();
    }
}
