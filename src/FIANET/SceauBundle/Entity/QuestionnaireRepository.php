<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
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
     * @param LivraisonType $livraisonType Instance de LivraisonType. Vaut null si aucun filtre souhaité.
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
        $listeReponsesIndicateurs,
        $livraisonType
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
                ->setParameter('dateFin', date('Y-m-d', strtotime($dateFin . ' + 1 day')));
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
            if ($questionnaireType->getParametrage()['indicateur']['type'] == 'reponse_id') {
                if ($listeReponsesIndicateurs['nullable']) {
                    $qb->andWhere($qb->expr()->orX(
                        $qb->expr()->in('r_ind.id', $listeReponsesIndicateurs['reponses']),
                        'r_ind.id IS NULL'
                    ));

                } else {
                    $qb->andWhere($qb->expr()->in('r_ind.id', $listeReponsesIndicateurs['reponses']));
                }

            } else {
                $filtresNote = '';
                for ($i = 0; $i < count($listeReponsesIndicateurs['reponses']); $i++) {
                    if ($i != 0) {
                        $filtresNote .= 'OR ';
                    }

                    $filtresNote .= '(qr_ind.note >=' . $listeReponsesIndicateurs['reponses'][$i]['min'] .
                        ' AND qr_ind.note <= ' . $listeReponsesIndicateurs['reponses'][$i]['max'] . ')';
                }

                if ($listeReponsesIndicateurs['nullable']) {
                    $qb->andWhere($qb->expr()->orX(
                        $qb->expr()->andX($filtresNote),
                        'r_ind.id IS NULL'
                    ));

                } else {
                    $qb->andWhere($filtresNote);
                }
            }

        } elseif ($listeReponsesIndicateurs['nullable']) {
            $qb->andWhere('r_ind.id IS NULL');
        }

        if ($livraisonType) {
            $qb->andWhere('lt.id = :ltid')
                ->setParameter('ltid', $livraisonType->getId());
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
     * @param LivraisonType $livraisonType Instance de LivraisonType. Vaut null si aucun filtre souhaité.
     *
     * @return int Le nombre de questionnaire
     */
    public function nbTotalQuestionnaires(
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $recherche,
        $listeReponsesIndicateurs,
        $livraisonType
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

        if ($livraisonType) {
            $qb->leftJoin('c.livraisonTypes', 'lt');
        }

        $qb = $this->restrictionsListeQuestionnaires(
            $qb,
            $site,
            $questionnaireType,
            $dateDebut,
            $dateFin,
            $recherche,
            $listeReponsesIndicateurs,
            $livraisonType
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
     * @param LivraisonType $livraisonType Instance de LivraisonType. Vaut null si aucun filtre souhaité.
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
        $livraisonType,
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

        if ($livraisonType) {
            $qb->leftJoin('c.livraisonTypes', 'lt');
        }

        $qb = $this->restrictionsListeQuestionnaires(
            $qb,
            $site,
            $questionnaireType,
            $dateDebut,
            $dateFin,
            $recherche,
            $listeReponsesIndicateurs,
            $livraisonType
        );

        if ($questionnaireType->getParametrage()['recommandation']['question_id']) {
            $qb->addSelect('qr_reco.note AS recommandation', 'quest_reco.valeurMax AS recommandationValeurMax')
                ->leftJoin(
                    'q.questionnaireReponses',
                    'qr_reco',
                    'WITH',
                    'qr_reco.question = :qid_reco'
                )->leftJoin('qr_reco.question', 'quest_reco')
                ->setParameter('qid_reco', $questionnaireType->getParametrage()['recommandation']['question_id']);

            if ($tri == 4) {
                $qb->orderBy('qr_reco.note', 'DESC');
            } elseif ($tri == 5) {
                $qb->orderBy('qr_reco.note', 'ASC');
            }
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

        // TODO : étudier le cache dans ce cas
        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getArrayResult();
    }

    /**
     * Rajoute les restrictions nécessaires pour sélectionner pour un site et pour un type de questionnaire :
     * - les questions globales illimitées
     * - les questions globales limitées dans le temps
     * - les questions personnalisées.
     * Le QueryBuilder passé en argument doit contenir un alias "q" pour l'entité Questionnaire et un alias "question"
     * pour l'entité Question.
     *
     * @param QueryBuilder $qb Instance de QueryBuilder
     * @param Site $site Instance de Site
     *
     * @return QueryBuilder
     */
    private function restrictionsQuestionsGlobalesPersos(QueryBuilder $qb, Site $site)
    {
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    'question.site IS NULL',
                    $qb->expr()->orX(
                        'question.dateDebut IS NULL',
                        $qb->expr()->andX(
                            'question.dateDebut <= q.dateReponse',
                            'question.dateFin >= q.dateReponse'
                        )
                    )
                ),
                $qb->expr()->andX(
                    'question.site = :sid',
                    'question.dateDebut <= q.dateReponse',
                    'question.dateFin >= q.dateReponse'
                )
            )
        )->setParameter('sid', $site->getId())
        ->andWhere($qb->expr()->eq('question.questionStatut', QuestionStatut::ACTIVEE));

        return $qb;
    }

    /**
     * Retourne l'ensemble de la structure d'un questionnaire d'un site avec les réponses répondues par l'internaute.
     *
     * @param Site $site Instance de Site
     * @param int $questionnaire_id Identifiant du questionnaire
     *
     * @return Questionnaire
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function structureQuestionnaireAvecReponses($site, $questionnaire_id)
    {
        $qb = $this->createQueryBuilder('q');

        $qb->innerJoin('q.site', 's')
            ->addSelect('s')
            ->leftJoin('q.sousSite', 'ss')
            ->addSelect('ss')
            ->leftJoin('q.commande', 'c')
            ->addSelect('c')
            ->leftJoin('c.livraisonTypes', 'lt')
            ->addSelect('lt')
            ->leftJoin('q.membre', 'm')
            ->addSelect('m')
            ->innerJoin('q.questionnaireType', 'qt')
            ->addSelect('qt')
            ->innerJoin('qt.questions', 'question')
            ->addSelect('question')
            ->innerJoin('question.questionType', 'questionType')
            ->addSelect('questionType')
            ->leftJoin('question.livraisonTypes', 'question_lt')
            ->addSelect('question_lt')
            ->innerJoin('question.reponses', 'r')
            ->addSelect('r')
            ->leftJoin('r.questionnaireReponses', 'qr', 'WITH', 'qr.questionnaire = q.id')
            ->addSelect('qr')
            ->leftJoin('qr.droitDeReponses', 'ddr', 'WITH', 'ddr.actif = true')
            ->addSelect('ddr')
            ->leftJoin('q.questionnaireLie', 'ql')
            ->addSelect('ql')
            ->where('q.id = :id')
            ->setParameter('id', $questionnaire_id)
            ->andWhere($qb->expr()->eq('r.reponseStatut', ReponseStatut::ACTIVEE))
            ->orderBy('question.ordre', 'ASC')
            ->addOrderBy('r.ordre', 'ASC');

        $qb = $this->restrictionsQuestionsGlobalesPersos($qb, $site);

        return $qb->getQuery()
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->useQueryCache(true)->useResultCache(true)->setResultCacheLifetime(300)
            ->getSingleResult(); // TODO vérifier le cache et tester les perfs
    }

    /**
     * Retourne les informations générales liées au questionnaire répondu (informations sur la commande, le membre,
     * le site, l'éventuel commentaire principal, l'éventuel droit de réponse lié au commentaire principal)
     *
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     *
     * @return Questionnaire[]
     */
    public function infosGeneralesQuestionnaire(Questionnaire $questionnaire, QuestionnaireType $questionnaireType)
    {
        $qb = $this->createQueryBuilder('q');
        
        $qb->leftJoin('q.commande', 'c')
            ->leftJoin('q.membre', 'm')
            ->leftJoin('q.sousSite', 'ss')
            ->leftJoin('q.site', 's')
            ->leftJoin(
                'q.questionnaireReponses',
                'qr_com',
                'WITH',
                'qr_com.question = :qid_com'
            )
            ->leftJoin(
                'qr_com.droitDeReponses',
                'ddr',
                'WITH',
                'ddr.actif = true'
            )
            ->addSelect('c')
            ->addSelect('m')
            ->addSelect('ss')
            ->addSelect('s')
            ->addSelect('qr_com')
            ->addSelect('ddr')
            ->setParameter('qid_com', $questionnaireType->getParametrage()['commentairePrincipal']);
        
        $qb->where('q.id=:id')
           ->setParameter('id', $questionnaire->getId());
        
        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult(); // TODO vérifier le cache
    }

    /**
     * Ajoute les restrictions à une requête pour sélectionner uniquement les questionnaires pouvant être relancés par
     * un site pour un type de questionnaire.
     *
     * @param QueryBuilder $qb Instance de QueryBuilder
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param string $dateDebut Date de début de la période
     * @param string $dateFin Date de fin de la période
     * @param integer $langue_id Identifiant de la langue des questionnaires
     *
     * @return QueryBuilder Le QueryBuilder modifié avec l'ajout des restrictions
     */
    private function restrictionsListeQuestionnairesARelancer(
        QueryBuilder $qb,
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $langue_id
    ) {
        $qb->andWhere('q.site = :sid')
            ->setParameter('sid', $site->getId())
            ->andWhere('q.questionnaireType = :qtid')
            ->setParameter('qtid', $questionnaireType->getId())
            ->andWhere('q.actif = true')
            ->andWhere('q.dateReponse IS NULL')
            ->andWhere('q.dateEnvoi >= :dateDebut')
            ->setParameter('dateDebut', $dateDebut)
            ->andWhere('q.dateEnvoi < :dateFin')
            ->setParameter('dateFin', $dateFin)
            ->andWhere('q.datePrevRelance IS NULL')
            ->andWhere('q.langue = :lid')
            ->setParameter('lid', $langue_id);

        return $qb;
    }

    /**
     * Retourne le nombre total de questionnaires pouvant être relancés par un site, pour un type de questionnaire et
     * pour une langue.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param string $dateDebut Date de début de la période
     * @param string $dateFin Date de fin de la période
     * @param integer $langue_id Identifiant de la langue des questionnaires
     *
     * @return int Le nombre de questionnaire
     */
    public function nbTotalQuestionnairesARelancer(
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $langue_id
    ) {
        $qb = $this->createQueryBuilder('q')
            ->select('COUNT(q.id)');

        $qb = $this->restrictionsListeQuestionnairesARelancer(
            $qb,
            $site,
            $questionnaireType,
            $dateDebut,
            $dateFin,
            $langue_id
        );

        return $qb->getQuery()->useQueryCache(true)->getSingleScalarResult();
    }

    /**
     * Retourne "un paquet" de questionnaires pouvant être relancés par un site pour un type de questionnaire et
     * pour une langue.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param string $dateDebut Date de début de la période
     * @param string $dateFin Date de fin de la période
     * @param integer $langue_id Identifiant de la langue des questionnaires
     * @param int $premierQuestionnaire Numéro du premier questionnaire retourné
     * @param int $nbQuestionnaires Nombre maximum de questionnaire retourné
     *
     * @return array Tableau de string
     */
    public function listeQuestionnairesARelancerParPaquet(
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $langue_id,
        $premierQuestionnaire,
        $nbQuestionnaires
    ) {
        $qb = $this->createQueryBuilder('q')
            ->select(
                'q.id',
                'q.email',
                'q.dateEnvoi'
            )->setFirstResult($premierQuestionnaire)
            ->setMaxResults($nbQuestionnaires);

        $qb = $this->restrictionsListeQuestionnairesARelancer(
            $qb,
            $site,
            $questionnaireType,
            $dateDebut,
            $dateFin,
            $langue_id
        );

        $qb->addOrderBy('q.dateEnvoi', 'DESC');

        return $qb->getQuery()->useQueryCache(true)->getArrayResult();
    }

    /**
     * Retourne les questionnaires pouvant être relancés par un site, pour un type de questionnaire et
     * pour une langue.
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param string $dateDebut Date de début de la période
     * @param string $dateFin Date de fin de la période
     * @param integer $langue_id Identifiant de la langue des questionnaires
     *
     * @return Questionnaire[]
     */
    public function listeQuestionnairesARelancer(
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $langue_id
    ) {
        $qb = $this->createQueryBuilder('q');

        $qb = $this->restrictionsListeQuestionnairesARelancer(
            $qb,
            $site,
            $questionnaireType,
            $dateDebut,
            $dateFin,
            $langue_id
        );

        return $qb->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * Retourne le questionnaire répondu suivant ou précédent répondant à certains critères
     *
     * @param Site $site Instance de Site
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param string $dateDebut Date de début de la période (peut être vide)
     * @param string $dateFin Date de fin de la période (peut être vide)
     * @param string $recherche Recherche de l'utilisateur (N°commande, Email, etc)
     * @param array $listeReponsesIndicateurs Tableau contenant les réponses des indicateurs à filtrer. Peut être vide.
     * @param LivraisonType $livraisonType Instance de LivraisonType. Vaut null si aucun filtre souhaité.
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * @param int $tri Numéro du tri à appliquer
     * @param $suivant Boolean vaut 1 pour avoir le questionnaire suivant, 0 avoir pour le questionnaire précédent
     *
     * @return Questionnaire[]|null instance de questionnaire trouvé ou null si pas de questionnaire trouvé
     */
    public function getQuestionnaireReponduNavigation(
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $recherche,
        $listeReponsesIndicateurs,
        $livraisonType,
        $questionnaire,
        $tri,
        $suivant
    ) {
        /***************************** TODO : Duplication de code, il faut mixer avec la requête qui existe déjà
         * sinon c'est une galère à maintenir **************************************/

        $qb = $this->createQueryBuilder('q')
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
            ->setMaxResults(1);

        if ($livraisonType) {
            $qb->leftJoin('c.livraisonTypes', 'lt');
        }

        $qb = $this->restrictionsListeQuestionnaires(
            $qb,
            $site,
            $questionnaireType,
            $dateDebut,
            $dateFin,
            $recherche,
            $listeReponsesIndicateurs,
            $livraisonType
        );

        if ($questionnaireType->getParametrage()['recommandation']['question_id']) {
            $qb->leftJoin(
                'q.questionnaireReponses',
                'qr_reco',
                'WITH',
                'qr_reco.question = :qid_reco'
            )->leftJoin('qr_reco.reponse', 'r_reco')
            ->setParameter('qid_reco', $questionnaireType->getParametrage()['recommandation']['question_id']);
        }

        $dateReponse = $questionnaire->getDateReponse();
        
        if ($questionnaire->getCommande()) {
            $dateCommande = $questionnaire->getCommande()->getDate();
        } else {
            $dateCommande = null;
        }
        
        /* ToDo : à revoir lorsqu'on fera le lot des questionnaires hors flux XML (gestion lorsqu'il n'y a pas de
         date de commande) */
        
        if ($tri == 0) {
            if ($dateCommande != null) {
                if ($suivant) {
                    $qb->andWhere('c.date < :dateCommande')
                        ->setParameter('dateCommande', $dateCommande);
                    $qb->orderBy('c.date', 'DESC');
                } else {
                    $qb->andWhere('c.date > :dateCommande')
                        ->setParameter('dateCommande', $dateCommande);
                    $qb->orderBy('c.date', 'ASC');
                }
            } else {
                if ($suivant) {
                    $qb->andWhere('q.dateReponse < :dateReponse')
                        ->setParameter('dateReponse', $dateReponse);
                    $qb->orderBy('c.date', 'DESC');
                    $qb->addOrderBy('q.dateReponse', 'DESC');
                } else {
                    $qb->andWhere('q.dateReponse > :dateReponse')
                        ->setParameter('dateReponse', $dateReponse);
                    $qb->orderBy('c.date', 'ASC');
                    $qb->addOrderBy('q.dateReponse', 'ASC');
                }
            }
        } elseif ($tri == 1) {
            if ($dateCommande != null) {
                if ($suivant) {
                    $qb->andWhere('c.date > :dateCommande')
                        ->setParameter('dateCommande', $dateCommande);
                    $qb->orderBy('c.date', 'ASC');
                } else {
                    $qb->andWhere('c.date < :dateCommande')
                        ->setParameter('dateCommande', $dateCommande);
                    $qb->orderBy('c.date', 'DESC');
                }
            } else {
                if ($suivant) {
                    $qb->andWhere('q.dateReponse < :dateReponse')
                        ->setParameter('dateReponse', $dateReponse);
                    $qb->orderBy('c.date', 'DESC');
                    $qb->addOrderBy('q.dateReponse', 'DESC');
                } else {
                    $qb->andWhere('q.dateReponse > :dateReponse')
                        ->setParameter('dateReponse', $dateReponse);
                    $qb->orderBy('c.date', 'ASC');
                    $qb->addOrderBy('q.dateReponse', 'ASC');
                }
            }
        } elseif ($tri == 2 or $tri == 4) {
         /* ToDo : à revoir si la MOA décide que la pagination doit se faire également sur le tri d'indice de
          recommandation (tri vaut 4) */
            if ($suivant) {
                $qb->andWhere('q.dateReponse < :dateReponse')
                    ->setParameter('dateReponse', $dateReponse);
                $qb->orderBy('q.dateReponse', 'DESC');
            } else {
                $qb->andWhere('q.dateReponse > :dateReponse')
                    ->setParameter('dateReponse', $dateReponse);
                $qb->orderBy('q.dateReponse', 'ASC');
            }
        } elseif ($tri == 3 or $tri == 5) {
        /* ToDo : à revoir si la MOA décide que la pagination doit se faire également sur le tri d'indice de
         recommandation (tri vaut 5) */
            if ($suivant) {
                $qb->andWhere('q.dateReponse > :dateReponse')
                    ->setParameter('dateReponse', $dateReponse);
                $qb->orderBy('q.dateReponse', 'ASC');
            } else {
                $qb->andWhere('q.dateReponse < :dateReponse')
                    ->setParameter('dateReponse', $dateReponse);
                $qb->orderBy('q.dateReponse', 'DESC');
            }
        }
        
        return $qb->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();
    }

    /**
     * Permet de savoir si un questionnaire actif existe bien à partir de son identifiant et s'il est bien relié au site
     * passé en argument.
     *
     * @param int $questionnaire_id Identifiant du questionnaire
     * @param Site $site Instance de Site
     *
     * @return array Si ok retourne un tableau conteant "1" sinon retourne un tableau vide
     */
    public function verifierExistenceEtLiaisonAvecSite($questionnaire_id, Site $site)
    {
        $qb = $this->createQueryBuilder('q');

        $qb->select('1')
            ->innerJoin('q.site', 's', 'WITH', 's.id = :sid')
            ->setParameter('sid', $site->getId())
            ->where('q.id = :id')
            ->setParameter('id', $questionnaire_id)
            ->andWhere('q.actif = true');

        return $qb->getQuery()->useQueryCache(true)->getScalarResult();
    }
}
