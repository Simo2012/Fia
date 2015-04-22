<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;

class QuestionnaireReponseRepository extends EntityRepository
{
    /**
     * Permet de savoir de droits de réponse actifs possède une réponse à un questionnaire
     * 
     * @param QuestionnaireReponse $questionnaireReponse Instance de Site
     *
     * @return int Le nombre de droits de réponse actifs trouvés
     */
    public function nbDroitDeReponseActif(QuestionnaireReponse $questionnaireReponse)
    {
        $qb = $this->createQueryBuilder('qr');
        
        $qb->select('COUNT(dr.id)')
                ->innerJoin('qr.droitDeReponses', 'dr')
                ->where('qr.id=:id')
                ->setParameter('id', $questionnaireReponse->getId()) 
                ->andWhere($qb->expr()->eq('dr.actif', 'true'));
        
        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getSingleScalarResult();
    }    
    
}
