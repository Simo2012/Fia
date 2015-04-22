<?php

namespace FIANET\SceauBundle\Entity\Extranet;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use FIANET\SceauBundle\Entity\Site;

class MenuElementRepository extends EntityRepository
{
    /**
     * Récupère l'ensemble du menu de l'extranet à afficher pour un site.
     *
     * @param Site $site Instance de Site
     *
     * @return MenuElement[]
     */
    public function menuExtranetCompletPourUnSite(Site $site)
    {
        $qb = $this->createQueryBuilder('mep');

        $qb->addSelect('mef')
            ->addSelect('optp')  //TODO, je ne pense pas qu'il soit nécessaire de récupérer les options du 1er niveau ?
            ->addSelect('optf')
            ->innerJoin('mep.questionnaireTypes', 'tq')
            ->leftJoin('mep.option', 'optp')  //TODO, je ne pense pas qu'il soit nécessaire de récupérer les options du 1er niveau ?
            ->leftJoin('mep.menuElementsFils', 'mef')
            ->leftJoin('mef.option', 'optf')
            ->where('tq.id=:id')
                ->setParameter('id', $site->getQuestionnairePersonnalisations()[0]->getQuestionnaireType()->getId()) // TODO, en fonction du questionnaire choisi dans la liste déroulante
            ->andWhere($qb->expr()->isNull('mep.menuElementParent'))
            ->andWhere($qb->expr()->eq('mep.actif', 'true'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('mef.id'),
                    $qb->expr()->eq('mef.actif', 'true')
                )
            )
            ->orderBy('mep.ordre', 'ASC')
            ->addOrderBy('mef.ordre', 'ASC');

        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();
    }
}
