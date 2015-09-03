<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use FIANET\SceauBundle\Cache\Cache;

class SiteRepository extends EntityRepository
{
    /**
     * Retourne l'ensemble des paramètrage CSV Auto pour chaque type de questionnaire d'un site.
     * Attention : le site doit avoir une garantie valide et être lié à ce mode d'administration.
     *
     * @param int $site_id Identifiant du site
     * @return Site|null Instance de Site ou null si le site n'est pas autorisé
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function parametragesCSVAuto($site_id)
    {
        $qb = $this->createQueryBuilder('s');

        // TODO : rajouter restriction sur les garanties
        $qb->innerJoin('s.questionnairePersonnalisations', 'qp')
            ->innerJoin('qp.questionnaireType', 'qt')
            ->innerJoin('qp.commandeCSVParametrage', 'ccp')
            ->innerJoin('ccp.commandeCSVColonnes', 'ccc')
            ->addSelect('qp')
            ->addSelect('qt')
            ->addSelect('ccp')
            ->addSelect('ccc')
            ->where('s.id = :id')
            ->setParameter('id', $site_id)
            ->andWhere($qb->expr()->eq('s.administrationType', AdministrationType::CSV_AUTO))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('qp.dateFin'),
                $qb->expr()->gt('qp.dateFin', 'CURRENT_DATE()')
            ))
            ->orderBy('s.id', 'ASC')
            ->addOrderBy('qp.id', 'ASC')
            ->addOrderBy('ccc.position', 'ASC');

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J)->getOneOrNullResult();
    }
}
