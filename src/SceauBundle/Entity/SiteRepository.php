<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use SceauBundle\Cache\Cache;

class SiteRepository extends EntityRepository
{
    /**
     * Retourne l'ensemble des paramètrage CSV auto et manuel pour chaque type de questionnaire d'un site.
     * Attention : le site doit avoir une garantie valide et être lié à ce mode d'administration.
     *
     * @param int $site_id Identifiant du site
     * @return Site|null Instance de Site ou null si le site n'est pas autorisé
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function parametragesCSV($site_id)
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
            ->andWhere($qb->expr()->orX(
                $qb->expr()->eq('s.administrationType', AdministrationType::CSV_AUTO),
                $qb->expr()->eq('s.administrationType', AdministrationType::CSV_AUTO)
            ))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('qp.dateFin'),
                $qb->expr()->gt('qp.dateFin', 'CURRENT_DATE()')
            ))
            ->orderBy('s.id', 'ASC')
            ->addOrderBy('qp.id', 'ASC')
            ->addOrderBy('ccc.position', 'ASC');

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J)->getOneOrNullResult();
    }

    /**
     * Retourne l'ensemble des sites avec pour seul questionnairePersonnalisation associé, le principal.
     * Utiliser pour la génération des widgets.
     *
     * @return Site[] Une collection d'instance de Site ou une collection vide
     */
    public function questionnairePrincipal()
    {
        // TODO : rajouter le filtrage sur le type de site
        return $this->createQueryBuilder('s')
            ->leftJoin('s.questionnairePersonnalisations', 'qp')
            ->where('qp.principal = :principal')
            ->setParameter('principal', true)
            ->getQuery()
            ->useResultCache(true, Cache::LIFETIME_1J)
            ->execute()
        ;
    }

    /**
     * Retourne les paramètrage CSV Auto pour un type de questionnaire d'un site.
     * Attention : le site doit avoir une garantie valide et être lié à ce mode d'administration.
     *
     * @param int $siteId                 Identifiant du site
     * @param int $questionnaireTypeId    Identifiant du questionnaire type
     *
     * @return Site|null Instance de Site ou null si le site n'est pas autorisé
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function parametragesCSVManuelByQuestionnaireType($siteId, $questionnaireTypeId)
    {
        $qb = $this->createQueryBuilder('s');

        // TODO : rajouter restriction sur les garanties
        $qb->innerJoin('s.questionnairePersonnalisations', 'qp')
            ->innerJoin('qp.questionnaireType', 'qt')
            ->innerJoin('qp.commandeCSVParametrage', 'ccp')
            ->addSelect('qp')
            ->addSelect('qt')
            ->addSelect('ccp')
            ->where($qb->expr()->eq('s.id', $siteId))
            ->andWhere($qb->expr()->eq('qp.id', $questionnaireTypeId))
            ->andWhere($qb->expr()->eq('s.administrationType', AdministrationType::CSV_MANUEL))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('qp.dateFin'),
                $qb->expr()->gt('qp.dateFin', 'CURRENT_DATE()')
            ));

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J)->getOneOrNullResult();
    }
}
