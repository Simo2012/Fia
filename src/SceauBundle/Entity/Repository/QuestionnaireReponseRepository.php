<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use SceauBundle\Entity\Indice;
use SceauBundle\Entity\IndiceType;
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
     * Requête qui permet de recuperer l'id de reponse principale
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
     * Requête qui permet de recuperer les commentaire
     */
    public function getComment($lpIdCommentaire, $lpQuestionnaireId, $lpQuestionId, $lpVert)
    {
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

    /**
     * Retourne la valeur d'un indice pour un site.
     *
     * @param Indice $indice Instance de Indice
     * @param int $site_id Identifiant du site
     */
    public function getValeurIndice(Indice $indice, $site_id)
    {
        $qb = $this->createQueryBuilder('qr');

        /* MOYENNE */
        if ($indice->getIndiceType()->getId() == IndiceType::MOYENNE) {
            $qb->select($qb->expr()->avg('qr.note'));
        }

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('qr.question', $indice->getQuestion()),
                $qb->expr()->eq('qr.question', $indice->getQuestion())
            )
        );
    }
}
