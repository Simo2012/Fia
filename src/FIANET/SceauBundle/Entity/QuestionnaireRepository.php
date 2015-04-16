<?php

namespace FIANET\SceauBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityRepository;

class QuestionnaireRepository extends EntityRepository
{
    /**
     * Retourne le nombre total de questionnaires répondus en fonction des filtres demandés.
     *
     * @param Site $site Instance de Site
     * @param string $dateDebut Date de début de la période (peut être vide)
     * @param string $dateFin Date de fin de la période (peut être vide)
     *
     * @return int Le nombre de questionnaire
     */
    public function nbTotalQuestionnaires(Site $site, $dateDebut, $dateFin)
    {
        $qb = $this->createQueryBuilder('q');

        $qb->select('COUNT(q.id)')
            ->where('q.site=:id')
                ->setParameter('id', $site->getId())
            ->andWhere('q.dateReponse IS NOT NULL');

        if ($dateDebut!= '') {
            $qb->andWhere('q.dateReponse >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        }

        if ($dateFin != '') {
            $qb->andWhere('q.dateReponse <= :dateFin')
                ->setParameter('dateFin', $dateFin);
        }

        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getSingleScalarResult();
    }

    /**
     * Retourne "un paquet" de questionnaires répondus en fonction des filtres et tris demandés.
     *
     * @param Site $site Instance de Site
     * @param string $dateDebut Date de début de la période (peut être vide)
     * @param string $dateFin Date de fin de la période (peut être vide)
     * @param int $premierQuestionnaire Numéro du premier questionnaire retourné
     * @param int $nbQuestionnaires Nombre maximum de questionnaire retourné
     * @param int $tri Numéro du tri à appliquer
     *
     * @return array Tableau de string
     */
    public function listeQuestionnaires(Site $site, $dateDebut, $dateFin, $premierQuestionnaire, $nbQuestionnaires, $tri)
    {
        $qb = $this->createQueryBuilder('q');

        $qb->select('q.email', 'q.dateReponse', 'c.date', 'm.nom', 'm.prenom')
            ->leftJoin('q.commande', 'c')
            ->leftJoin('q.membre', 'm')
            ->where('q.site=:id')
                ->setParameter('id', $site->getId())
            ->andWhere('q.dateReponse IS NOT NULL')
            ->setFirstResult($premierQuestionnaire)
            ->setMaxResults($nbQuestionnaires);

        if ($dateDebut!= '') {
            $qb->andWhere('q.dateReponse >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        }

        if ($dateFin != '') {
            $qb->andWhere('q.dateReponse <= :dateFin')
                ->setParameter('dateFin', $dateFin);
        }

        if ($tri == 0) {
            $qb->orderBy('c.date', 'DESC');
        } elseif ($tri == 1) {
            $qb->orderBy('c.date', 'ASC');
        } elseif ($tri == 2) {
            $qb->orderBy('q.dateReponse', 'DESC');
        } elseif ($tri == 3) {
            $qb->orderBy('q.dateReponse', 'ASC');
        }

        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getArrayResult();
    }
}
