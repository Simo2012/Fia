<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class QuestionnaireRepository extends EntityRepository
{
    /**
     * Ajoute les restrictions à une requête pour sélectionner uniquement les questionnaires qui répondent à un certain
     * nombre de filtres.
     *
     * @param QueryBuilder $qb Instance de QueryBuilder
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param string $dateDebut Date de début de la période (peut être vide)
     * @param string $dateFin Date de fin de la période (peut être vide)
     * @param string $recherche Recherche de l'utilisateur (N°commande, Email, etc)
     * @param array $listeReponsesIndicateurs Tableau contenant les réponses des indicateurs à filtrer. Peut être vide.
     *
     * @return QueryBuilder Le QueryBuilder modifié avec l'ajout des restrictions
     */
    private function restrictionsListeQuestionnaires(
        QueryBuilder $qb,
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $recherche,
        $listeReponsesIndicateurs
    ) {
        $qb->andWhere('q.site = :sid')
            ->andWhere('q.questionnaireType = :qtid')
            ->andWhere('q.actif = true')
            ->andWhere('q.dateReponse IS NOT NULL')
            ->setParameter('sid', $site->getId())
            ->setParameter('qtid', $questionnaireType->getId());

        if ($dateDebut != '') {
            $qb->andWhere('q.dateReponse >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        }

        if ($dateFin != '') {
            $qb->andWhere('q.dateReponse <= :dateFin')
                ->setParameter('dateFin', $dateFin);
        }

        // TODO il faudra éventuellement indexer ces colonnes, sinon ça risque de bouffer un max de ressource
        if ($recherche != '') {
            $qb->andWhere(
                $qb->expr()->orX(
                    'q.email = :recherche',
                    'c.reference = :recherche',
                    'c.nom = :recherche',
                    'm.pseudo = :recherche'
                )
            )->setParameter('recherche', $recherche);
        }

        if ($listeReponsesIndicateurs['reponses']) {
            if ($listeReponsesIndicateurs['nullable']) {
                $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->in('r_ind.id', $listeReponsesIndicateurs['reponses']),
                    'r_ind.id IS NULL'
                ));

            } else {
                $qb->andWhere($qb->expr()->in('r_ind.id', $listeReponsesIndicateurs['reponses']));
            }

        } elseif ($listeReponsesIndicateurs['nullable']) {
            $qb->andWhere('r_ind.id IS NULL');
        }

        return $qb;
    }

    /**
     * Retourne le nombre total de questionnaires répondus en fonction des filtres demandés.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param string $dateDebut Date de début de la période (peut être vide)
     * @param string $dateFin Date de fin de la période (peut être vide)
     * @param string $recherche Recherche de l'utilisateur (N°commande, Email, etc)
     * @param array $listeReponsesIndicateurs Tableau contenant les réponses des indicateurs à filtrer. Peut être vide.
     *
     * @return int Le nombre de questionnaire
     */
    public function nbTotalQuestionnaires(
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $recherche,
        $listeReponsesIndicateurs
    ) {
        $qb = $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->leftJoin('q.commande', 'c')
            ->leftJoin('q.membre', 'm')
            ->leftJoin(
                'q.questionnaireReponses',
                'qr_ind',
                'WITH',
                'qr_ind.question = :qid_ind'
            )
            ->leftJoin('qr_ind.reponse', 'r_ind')
            ->setParameter('qid_ind', $questionnaireType->getParametrage()['indicateur']['question_id']);

        $qb = $this->restrictionsListeQuestionnaires(
            $qb,
            $site,
            $questionnaireType,
            $dateDebut,
            $dateFin,
            $recherche,
            $listeReponsesIndicateurs
        );

        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getSingleScalarResult();
    }

    /**
     * Retourne "un paquet" de questionnaires répondus en fonction des filtres et tris demandés.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param string $dateDebut Date de début de la période (peut être vide)
     * @param string $dateFin Date de fin de la période (peut être vide)
     * @param string $recherche Recherche de l'utilisateur (N°commande, Email, etc)
     * @param array $listeReponsesIndicateurs Tableau contenant les réponses des indicateurs à filtrer. Peut être vide.
     * @param int $premierQuestionnaire Numéro du premier questionnaire retourné
     * @param int $nbQuestionnaires Nombre maximum de questionnaire retourné
     * @param int $tri Numéro du tri à appliquer
     *
     * @return array Tableau de string
     */
    public function listeQuestionnaires(
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $recherche,
        $listeReponsesIndicateurs,
        $premierQuestionnaire,
        $nbQuestionnaires,
        $tri
    ) {
        $qb = $this->createQueryBuilder('q')
            ->select(
                'q.id',
                'q.email',
                'q.dateReponse',
                'c.date AS dateCommande',
                'm.nom',
                'm.prenom',
                'qr_com.commentaire',
                'qr_com.id AS commentaireID',
                'qr_ind.note AS indicateurNote',
                'r_ind.id AS indicateurReponseID'
            )
            ->leftJoin('q.commande', 'c')
            ->leftJoin('q.membre', 'm')
            ->leftJoin(
                'q.questionnaireReponses',
                'qr_com',
                'WITH',
                'qr_com.question = :qid_com'
            )
            ->leftJoin(
                'q.questionnaireReponses',
                'qr_ind',
                'WITH',
                'qr_ind.question = :qid_ind'
            )
            ->leftJoin('qr_ind.reponse', 'r_ind')
            ->setParameter('qid_com', $questionnaireType->getParametrage()['commentairePrincipal'])
            ->setParameter('qid_ind', $questionnaireType->getParametrage()['indicateur']['question_id'])
            ->setFirstResult($premierQuestionnaire)
            ->setMaxResults($nbQuestionnaires);

        $qb = $this->restrictionsListeQuestionnaires(
            $qb,
            $site,
            $questionnaireType,
            $dateDebut,
            $dateFin,
            $recherche,
            $listeReponsesIndicateurs
        );

        if ($questionnaireType->getParametrage()['recommandation']['question_id']) {
            $qb->addSelect('qr_reco.note AS recommandation', 'r_reco.valeurMax AS recommandationValeurMax')
                ->leftJoin(
                    'q.questionnaireReponses',
                    'qr_reco',
                    'WITH',
                    'qr_reco.question = :qid_reco'
                )->leftJoin('qr_reco.reponse', 'r_reco')
                ->setParameter('qid_reco', $questionnaireType->getParametrage()['recommandation']['question_id']);

            if ($tri == 3) {
                $qb->orderBy('qr_reco.note', 'ASC');
            } elseif ($tri == 4) {
                $qb->orderBy('qr_reco.note', 'DESC');
            }
        }

        if ($tri == 0) {
            $qb->orderBy('c.date', 'DESC');
        } elseif ($tri == 1) {
            $qb->orderBy('c.date', 'ASC');
        } elseif ($tri == 2) {
            $qb->orderBy('q.dateReponse', 'DESC');
        }

        echo $questionnaireType->getParametrage()['commentairePrincipal'];
        
        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getArrayResult();
    }

    /**
     * Retourne les informations liées au questionnaire répondu (informations sur la commande, le membre, le site,
     * le questionnaire répondu, l'indicateur de satisfaction )
     *
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     *
     * @return Questionnaire[]
     */
    public function infosGeneralesQuestionnaire(
            Questionnaire $questionnaire, 
            QuestionnaireType $questionnaireType
    ){
        $qb = $this->createQueryBuilder('q');
         
        $qb->leftJoin('q.commande', 'c')
            ->leftJoin('q.membre', 'm')
            ->leftJoin('q.sousSite', 'ss')
            ->leftJoin('q.site', 's')
            ->leftJoin(
                'q.questionnaireReponses',
                'qr_ind',
                'WITH',
                'qr_ind.question = :qid_ind'
            )
            ->leftJoin('qr_ind.reponse', 'r_ind')
            ->addSelect('c')
            ->addSelect('m')
            ->addSelect('ss')
            ->addSelect('s')
            ->addSelect('qr_ind')
            ->addSelect('r_ind') 
            ->setParameter('qid_ind', $questionnaireType->getParametrage()['indicateur']['question_id'])
            ->where('q.id=:id')
            ->setParameter('id', $questionnaire->getId())
            ->andWhere('q.dateReponse IS NOT NULL');

        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();
    }
}
