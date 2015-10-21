<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use SceauBundle\Entity\QuestionnaireReponse;

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
    
    /**
     * Requette qui permet de recuperer l'id de reponse principale
     */
    public function getRespone($lpQuestionId, $lpQuestionnaireId)
    {
        $loQuery = $this->createQueryBuilder('qr')
                        ->select('r.id')
                        ->join('qr.question', 'q')
                        ->join('qr.reponse', 'r')
                        ->join('qr.questionnaire', 'qtre')
                        ->where('q.id = :QuestionId')
                        ->andWhere('qtre.id = :QuestionnaireId')
                        ->setParameter('QuestionId', $lpQuestionId)
                        ->setParameter('QuestionnaireId', $lpQuestionnaireId)
                        ->orderBy('qr.id', 'DESC')
                        ->setMaxResults(1);
        return $loQuery->getQuery()->getSingleResult();
    }
    
    /**
     * Requete qui permet de recuperer les commentaire
     */
    public function getComment($lpIdCommentaire, $lpQuestionnaireId, $lpQuestionId, $lpVert) {
        
        $idResponse = $this->getRespone($lpQuestionId, $lpQuestionnaireId);
        if (in_array($idResponse['id'], $lpVert)) {
            $loQuery = $this->createQueryBuilder('qr')
                        ->select('qr, qtre, m')
                        ->join('qr.question', 'q')
                        ->join('qr.reponse', 'r')
                        ->join('qr.questionnaire', 'qtre')
                        ->leftJoin('qtre.membre', 'm')
                        ->where('q.id = :QuestionId')
                        ->andWhere('qtre.id = :QuestionnaireId')
                        ->setParameter('QuestionId', $lpIdCommentaire)
                        ->setParameter('QuestionnaireId', $lpQuestionnaireId)
                        ->orderBy('qr.id', 'DESC')
                        ->setMaxResults(1);
            return $loQuery->getQuery()->getArrayResult();
        }
        return null;
        
    }
    
}
