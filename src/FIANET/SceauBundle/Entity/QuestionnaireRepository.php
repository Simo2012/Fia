<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class QuestionnaireRepository extends EntityRepository
{
    /**
     * Ajoute les restrictions à une requête pour sélectionner uniquement les questionnaires qui répondent à un certain
     * nombre de filtre.
     *
     * @param QueryBuilder $qb Instance de QueryBuilder
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param string $dateDebut Date de début de la période (peut être vide)
     * @param string $dateFin Date de fin de la période (peut être vide)
     * @param string $recherche Recherche de l'utilisateur (N°commande, Email, etc)
     *
     * @return QueryBuilder Le QueryBuilder modifié avec l'ajout des restrictions
     */
    private function restrictionsListeQuestionnaires(
        QueryBuilder $qb,
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $recherche
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
     *
     * @return int Le nombre de questionnaire
     */
    public function nbTotalQuestionnaires(
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $recherche
    ) {
        $qb = $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->leftJoin('q.commande', 'c')
            ->leftJoin('q.membre', 'm');

        $qb = $this->restrictionsListeQuestionnaires($qb, $site, $questionnaireType, $dateDebut, $dateFin, $recherche);

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
        $premierQuestionnaire,
        $nbQuestionnaires,
        $tri
    ) {
        $qb = $this->createQueryBuilder('q')
            ->select(
                'q.email',
                'q.dateReponse',
                'c.date AS dateCommande',
                'm.nom',
                'm.prenom',
                'qr_com.commentaire',
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

        $qb = $this->restrictionsListeQuestionnaires($qb, $site, $questionnaireType, $dateDebut, $dateFin, $recherche);

        if ($questionnaireType->getParametrage()['recommendation']['question_id']) {
            $qb->addSelect('qr_reco.note AS recommendation', 'r_reco.valeurMax AS recommendationValeurMax')
                ->leftJoin(
                    'q.questionnaireReponses',
                    'qr_reco',
                    'WITH',
                    'qr_reco.question = :qid_reco'
                )->leftJoin('qr_reco.reponse', 'r_reco')
                ->setParameter('qid_reco', $questionnaireType->getParametrage()['recommendation']['question_id']);
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
    
    /**
     * Retourne les informations liées au questionnaire répondu (informations sur la commande, le membre, le site,
     * le questionnaire répondu)
     *
     * @param Questionnaire $questionnaire Instance de Questionnaire
     *
     * @return Questionnaire[]
     */
    public function infosGeneralesQuestionnaire(Questionnaire $questionnaire)
    {
        $qb = $this->createQueryBuilder('q');
        
        // ToDo : est-il préférable de sélectionner des attributs précis plutôt que l'ensemble ? à confirmer
        //$qb->select('c.reference', 'm.prenom', 'm.nom','m.pseudo', 'c.email', 'c.montant', 'c.date', 'ss.nom', 's.nom', 'q.dateReponse' )
        $qb->leftJoin('q.commande', 'c')
            ->leftJoin('q.membre', 'm')
            ->leftJoin('q.sousSite', 'ss')
            ->leftJoin('q.site', 's')
            ->addSelect('c')
            ->addSelect('m')
            ->addSelect('ss')
            ->addSelect('s')
            ->where('q.id=:id')
                ->setParameter('id', $questionnaire->getId())
            ->andWhere('q.dateReponse IS NOT NULL')
            ;
        
        // ->innerJoin('q.questionnaireType', 'qt')
        // ->leftJoin('q.questionnaireReponse', 'qr') 
        // ->leftJoin('q.questionnairePersonnalisation', 'qp')
        // ->leftJoin('qp.version', 'v')
        // ->leftJoin('qt.delaiEnvoi', 'de')
        // ->leftJoin('qp.delaiEnvoi', 'de')
        // ->leftJoin('qt.delaiReception', 'dr')
        // ->leftJoin('qp.delaiReception', 'dr')
        
        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();
    }
}
