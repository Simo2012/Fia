<?php

namespace FIANET\SceauBundle\Service;

use Doctrine\ORM\EntityManager;
use FIANET\SceauBundle\Entity\LivraisonType;
use FIANET\SceauBundle\Entity\Questionnaire;
use FIANET\SceauBundle\Entity\QuestionnaireReponse;
use FIANET\SceauBundle\Entity\QuestionnaireType;
use FIANET\SceauBundle\Entity\Site;

class QuestionnaireRepondu
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /************* TODO : contrôler la logique de la succession des fonctions verifier...().
     * Ne peut-on pas fusionner le tout ? *****************/

    /**
     * Méthode qui permet de savoir si un Questionnaire existe bien et qu'il est actif
     *
     * @param integer $questionnaire_id Identifiant du Questionnaire
     *
     * @return Boolean true s'il existe bien et qu'il est actif
     */
    public function verifierValiditeQuestionnaire($questionnaire_id)
    {
        if (is_numeric($questionnaire_id)) {
            $questionnaire = $this->em
                ->getRepository('FIANETSceauBundle:Questionnaire')
                ->find($questionnaire_id);

            if ($questionnaire) {
                if ($questionnaire->getActif()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Méthode qui permet de savoir si un QuestionnaireReponse existe bien
     *
     * @param integer $questionnaireReponse_id Identifiant du QuestionnaireReponse
     *
     * @return Boolean true s'il existe bien
     */
    public function verifierValiditeQuestionnaireReponse($questionnaireReponse_id)
    {
        if (is_numeric($questionnaireReponse_id)) {
            $questionnaireReponse = $this->em
                ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                ->find($questionnaireReponse_id);

            if ($questionnaireReponse) {
                return true;
            }
        }

        return false;
    }

    /**
     * Méthode qui permet de savoir si un QuestionnaireReponse donné est bien lié à un Questionnaire donné
     *
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * @param QuestionnaireReponse $questionnaireReponse Instance de QuestionnaireReponse
     *
     * @return Boolean true s'il y a cohérence
     */
    public function coherenceQuestionnaireVsQuestionnaireReponse(
        Questionnaire $questionnaire,
        QuestionnaireReponse $questionnaireReponse
    ) {
        if ($questionnaire->getQuestionnaireReponses()) {
            if ($questionnaire->getQuestionnaireReponses()->contains($questionnaireReponse)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Méthode qui permet de savoir si un Questionnaire donné est bien lié à un Site donné
     *
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * @param Site $site Instance de Site
     *
     * @return Boolean true s'il y a cohérence
     */
    public function coherenceQuestionnaireVsSite(Questionnaire $questionnaire, Site $site)
    {

        return ($questionnaire->getSite() == $site) ? true : false;
    }

    /**
     * Méthode qui permet de savoir si les arguments donnés pour l'appel d'un droit de réponse sont cohérents
     *
     * @param Site $site Instance de Site
     * @param integer $questionnaire_id Identifiant du Questionnaire
     * @param integer $questionnaireReponse_id Identifiant du QuestionnaireReponse
     *
     * @return Boolean true s'il y a cohérence
     */
    public function coherenceArgumentsDroitDeReponse(Site $site, $questionnaire_id, $questionnaireReponse_id)
    {
        if ($this->verifierValiditeQuestionnaire($questionnaire_id)) {
            $questionnaire = $this->em
                ->getRepository('FIANETSceauBundle:Questionnaire')
                ->find($questionnaire_id);

            if ($this->coherenceQuestionnaireVsSite($questionnaire, $site)) {
                if ($this->verifierValiditeQuestionnaireReponse($questionnaireReponse_id)) {
                    $questionnaireReponse = $this->em
                        ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                        ->find($questionnaireReponse_id);

                    if ($this->coherenceQuestionnaireVsQuestionnaireReponse($questionnaire, $questionnaireReponse)) {
                        dump($questionnaire_id);
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Méthode qui permet de savoir si les arguments donnés pour l'appel d'un ajout de droit de réponse sont cohérents
     *
     * @param Site $site Instance de Site
     * @param integer $questionnaire_id Identifiant du Questionnaire
     * @param integer $questionnaireReponse_id Identifiant du QuestionnaireReponse
     *
     * @return Boolean true s'il y a cohérence
     */
    public function coherenceArgumentsDroitDeReponseAjout(Site $site, $questionnaire_id, $questionnaireReponse_id)
    {
        if ($this->coherenceArgumentsDroitDeReponse($site, $questionnaire_id, $questionnaireReponse_id)) {
            $questionnaireReponse = $this->em
                ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                ->find($questionnaireReponse_id);

            /* S'il n'existe pas déjà un droit de réponse actif pour le questionnaireReponse alors on pourra ajouter
             un droit de réponse */
            $nb_DroitDeReponse_Actif = $this->em
                ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                ->nbDroitDeReponseActif($questionnaireReponse);
            if ($nb_DroitDeReponse_Actif == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Méthode qui permet de savoir si les arguments donnés pour l'appel d'affichage des détails d'un questionnaire
     * sont cohérents.
     *
     * @param Site $site Instance de Site
     * @param integer $questionnaire_id Identifiant du Questionnaire
     *
     * @return Boolean true s'il y a cohérence
     */
    public function coherenceArgumentsDetailsQuestionnaire(Site $site, $questionnaire_id)
    {
        if ($this->verifierValiditeQuestionnaire($questionnaire_id)) {
            $questionnaire = $this->em
                ->getRepository('FIANETSceauBundle:Questionnaire')
                ->find($questionnaire_id);

            if ($this->coherenceQuestionnaireVsSite($questionnaire, $site)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Méthode qui permet de retourner toutes les questions d'un questionnaire répondu
     *
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     *
     * @return Array $listeQuestionsReponses tableau contenant l'ensemble des questions et réponses du questionnaire
     *
     * @throw Exception Le QuestionnaireType ne possède pas de question
     */
//    public function getAllQuestionsReponses(Questionnaire $questionnaire, QuestionnaireType $questionnaireType)
//    {
//
//        $oQuestions = $this->em->getRepository('FIANETSceauBundle:Question')->getAllQuestionsOrdered($questionnaireType);
//
//        if (!$oQuestions) {
//            throw new Exception('Le type de questionnaire n°' . $questionnaireType->getId() . ' ne possède pas de question');
//        }
//
//        $listeQuestionsReponses = array();
//
//        $i = 0;
//
//        foreach ($oQuestions as $question) {
//            $listeQuestionsReponses[$i]['question'] = $question;
//
//            $commentairePrincipal = isset($questionnaireType->getParametrage()['commentairePrincipal']) ? $questionnaireType->getParametrage()['commentairePrincipal'] : null;
//
//            $listeQuestionsReponses[$i]['commentairePrincipal'] = ($commentairePrincipal != null && $question->getId() == $commentairePrincipal) ? true : false;
//
//            $listeQuestionsReponses[$i]['questionPrimaire'] = $question->getQuestionPrimaire();
//            $listeQuestionsReponses[$i]['questionsSecondaires'] = $question->getQuestionsSecondaires();
//
//            $listeQuestionsReponses[$i]['cacher'] = false;
//
//            $influenceFianet = isset($questionnaireType->getParametrage()['influenceFianet']) ? $questionnaireType->getParametrage()['influenceFianet'] : null;
//
//            /* ToDo : condition à revoir car la question sur l'influence de FIA-NET devrait peut-être être visible sur d'autres interfaces (à voir selon les specs des autres lots...) */
//            if ($influenceFianet != null && $question->getId() == $influenceFianet) {
//                $listeQuestionsReponses[$i]['cacher'] = true;
//            } else {
//
//                $listeQuestionsReponses[$i]['reponses'] = $this->getListeReponses($questionnaire, $question);
//
//                $nbReponses = 0;
//                foreach ($listeQuestionsReponses[$i]['reponses'] as $reponse) {
//                    if (is_object($reponse->getQuestionnaireReponses()[0])) {
//                        $nbReponses++;
//                    }
//                }
//
//                /* Si la question n'est pas cachée par défaut et que l'internaute n'y a pas répondu; on indique le message "Pas de réponse à cette question" */
//                if ($question->getCache() == false && $nbReponses == 0) {
//                    $listeQuestionsReponses[$i]['questionRepondue'] = false;
//                } else {
//                    $listeQuestionsReponses[$i]['questionRepondue'] = true;
//
//                    if ($question->getCache() == true) {
//
//                        /* Si la question ou réponse liée a été répondue on affichera la question étudiée */
//                        $listeQuestionsReponses[$i]['cacher'] = true;
//                        $reponseLieeRepondue = null;
//                        $questionLieeRepondue = null;
//
//                        if ($question->getVisible()['reponse_id'] && is_numeric($question->getVisible()['reponse_id'])) {
//                            $reponseLieeRepondue = $this->em
//                                ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
//                                ->findOneBy(array('reponse' => $question->getVisible()['reponse_id'], 'questionnaire' => $questionnaire));
//
//                            if ($reponseLieeRepondue != null) $listeQuestionsReponses[$i]['cacher'] = false;
//                        } else {
//                            if ($question->getVisible()['question_id'] && is_numeric($question->getVisible()['question_id'])) {
//                                $questionLieeRepondue = $this->em
//                                    ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
//                                    ->findOneBy(array('question' => $question->getVisible()['question_id'], 'questionnaire' => $questionnaire));
//
//                                if ($questionLieeRepondue != null) $listeQuestionsReponses[$i]['cacher'] = false;
//                            }
//                        }
//
//                    }
//
//                }
//
//            }
//
//            $i++;
//        }
//
//        return $listeQuestionsReponses;
//    }

    /**
     * Retourne un tableau contenant le questionnaire suivant et/ou précédent à afficher selon certains critères
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
     *
     * @return array Tableau avec des instances de Questionnaire ou valeurs null
     */
    public function getNavigation(
        Site $site,
        QuestionnaireType $questionnaireType,
        $dateDebut,
        $dateFin,
        $recherche,
        $listeReponsesIndicateurs,
        $livraisonType,
        $questionnaire,
        $tri
    ) {
        $navigation = array();

        $navigation['precedent'] = $this->em->getRepository('FIANETSceauBundle:Questionnaire')
            ->getQuestionnaireReponduNavigation(
                $site,
                $questionnaireType,
                $dateDebut,
                $dateFin,
                $recherche,
                $listeReponsesIndicateurs,
                $livraisonType,
                $questionnaire,
                $tri,
                0
            );

        $navigation['suivant'] = $this->em->getRepository('FIANETSceauBundle:Questionnaire')
            ->getQuestionnaireReponduNavigation(
                $site,
                $questionnaireType,
                $dateDebut,
                $dateFin,
                $recherche,
                $listeReponsesIndicateurs,
                $livraisonType,
                $questionnaire,
                $tri,
                1
            );

        return $navigation;
    }

    /**
     * Remplace les variables contenus dans les libellés des questions par leurs valeurs.
     * Exemple : NOM_SITE => Cdiscount
     *
     * @param Questionnaire $questionnaire Instance de Questionnaire
     */
    private function remplacerVariablesDansLibelles(Questionnaire $questionnaire)
    {
        foreach ($questionnaire->getQuestionnaireType()->getQuestions() as $question) {
            $question->setLibelle(
                str_replace('##NOM_SITE##', $questionnaire->getSite()->getNom(), $question->getLibelle())
            );
        }
    }

    /**
     * Retourne l'ensemble de la structure d'un questionnaire avec ses réponses répondues. Si ce questionnaire est lié à
     * un 2ème questionnaire, il est également récupéré.
     *
     * Le but du résultat final est d'être affiché dans un template. Il y a donc un certains nombre de traitements qui
     * sont effectués.
     *
     * @param Site $site Instance de Site
     * @param int $questionnaire_id Identifiant du questionnaire
     *
     * @return array Tableau comportant plusieurs clés :
     *     "questionnaire1"  => premier questionnaire
     *     "questionnaire2" => 2ème questionnaire s'il existe ou null
     */
    public function recupStructureQuestionnaireAvecReponses(Site $site, $questionnaire_id)
    {
        $questionnaireDemande = $this->em->getRepository('FIANETSceauBundle:Questionnaire')
            ->structureQuestionnaireAvecReponses($site, $questionnaire_id);

        if ($questionnaireDemande->getQuestionnaireLie() != null) {
            /* C'est un Q2 : on récupère les infos du premier questionnaire lié */
            $questionnaire1 = $this->em->getRepository('FIANETSceauBundle:Questionnaire')
                ->structureQuestionnaireAvecReponses($site, $questionnaireDemande->getQuestionnaireLie()->getId());
            $questionnaire2 = $questionnaireDemande;

        } elseif ($questionnaireDemande->getQuestionnaireType()->getQuestionnaireTypeSuivant() == null) {
            /* C'est un QU : rien d'autre à faire */
            $questionnaire1 = $questionnaireDemande;
            $questionnaire2 = null;

        } else {
            /* C'est un Q1 : on doit récupérer le deuxième questionnaire */
            $questionnaire1 = $questionnaireDemande;

            $questionnaire2_id = $this->em->getRepository('FIANETSceauBundle:Questionnaire')
                ->findBy(array('questionnaireLie' => $questionnaireDemande));

            $questionnaire2 = $this->em->getRepository('FIANETSceauBundle:Questionnaire')
                ->structureQuestionnaireAvecReponses($site, $questionnaire2_id);
        }

        $this->remplacerVariablesDansLibelles($questionnaire1);
        if ($questionnaire2) {
            $this->remplacerVariablesDansLibelles($questionnaire2);
        }

        return array('questionnaire1' => $questionnaire1, 'questionnaire2' => $questionnaire2);
    }
}
