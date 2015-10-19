<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;

class QuestionnaireReponseRepository extends EntityRepository
{
    /**
     * Permet de savoir le nombre de droits de réponse actifs que possède une réponse à un questionnaire
     *
     * @param QuestionnaireReponse $questionnaireReponse Instance de QuestionnaireReponse
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
        
        return $qb->getQuery()->getSingleScalarResult();
    }
}
