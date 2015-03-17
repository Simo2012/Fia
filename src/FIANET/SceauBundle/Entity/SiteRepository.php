<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class SiteRepository extends EntityRepository
{
    /**
     * TODO
     */
    public function chargerSiteAvecPackageEtOptionsSouscrites($nom) // TODO à changer : mettre identifiant à la place une fois la connexion réalisée complétement
    {
        $qb = $this->createQueryBuilder('s');

        $qb->addSelect('p')
            ->addSelect('po')
            ->addSelect('so')
            ->addSelect('o')
            ->innerJoin('s.package', 'p')
            ->innerJoin('p.packageOptions', 'po')
            ->leftJoin('s.siteOptions', 'so')
            ->leftJoin('so.option', 'o')
            ->where('s.nom=:nom')
                ->setParameter('nom', $nom);
        // TODO ajouter condition sur les options souscrites (date)


        return $qb->getQuery()->getSingleResult();
    }
}
