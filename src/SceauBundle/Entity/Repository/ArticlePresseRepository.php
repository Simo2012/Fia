<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * ArticlePresseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticlePresseRepository extends EntityRepository
{
    public function getAllArticlePresse()
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->where('a.published = true');

        return $adapter = new DoctrineORMAdapter($queryBuilder);

    }



}
