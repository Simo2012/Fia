<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use SceauBundle\Cache\Cache;
use SceauBundle\Entity\QuestionnaireType;

class QuestionnaireTypeRepository extends EntityRepository
{
    /**
     * Permet de récupérer une instance de QuestionnaireType à partir de son identifiant.
     * Le résultat de la requête est mis en cache pendant 1 jour.
     *
     * @param int $id Identifiant du type de Questionnaire
     *
     * @return QuestionnaireType
     */
    public function get($id)
    {
        $qb = $this->createQueryBuilder('qt')
            ->where('qt.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J, "questionnaireType_$id")->getSingleResult();
    }
}
