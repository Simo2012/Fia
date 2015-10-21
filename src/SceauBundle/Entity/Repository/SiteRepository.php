<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use SceauBundle\Cache\Cache;
use SceauBundle\Entity\AdministrationType;
use SceauBundle\Entity\Site;

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
     * Fonction pour récuperer les sites prenium
     */
    public function getPreniumSite()
    {
        $loQuery = $this->createQueryBuilder('s')
            ->select('s, sc ,c, qp, qt')
            ->join('s.siteCategories', 'sc')
            ->join('sc.categorie', 'c')
            ->join('s.questionnairePersonnalisations', 'qp')
            ->join('qp.questionnaireType', 'qt')
            ->where('qp.principal = true')
            ->andWhere('sc.principal = true')
            ->setMaxResults(4);

        return $loQuery->getQuery()->getArrayResult();
    }

    /**
     * Fonction pour retourner le nombre de site
     */
    public function getCount()
    {
        return $this->createQueryBuilder('s')
            ->select('count(s)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
