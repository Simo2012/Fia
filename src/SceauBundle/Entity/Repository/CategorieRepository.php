<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategorieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategorieRepository extends EntityRepository
{
    public function getActifCategories() {
        $loQuery = $this->createQueryBuilder('c')
                        ->select('c, cs')
                        ->join('c.categoriesSecondaires', 'cs')
                        ->where('c.actif = true')
                        ->andWhere('c.accueil = true')
                        ->andWhere('cs.accueil = true')
                        ->andWhere('cs.actif = true');
        return $loQuery->getQuery()->getResult();
    }
}
