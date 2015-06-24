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
        /* TODO : optimiser la vérification du droit de réponse (trop de requêtes) */
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

        /* TODO : à revoir */
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
     * Cette méthode autorise l'affichage des questions cachées si les questions et réponses dont elles dépendent ont
     * été répondues. La structure du questionnaire passé en argument est directement modifiée.
     *
     * @param Questionnaire $questionnaire Instance de Questionnaire
     */
    private function gestionAffichageQuestionCachee(Questionnaire $questionnaire)
    {
        $questions = $questionnaire->getQuestionnaireType()->getQuestions();

        foreach ($questions as &$question) {
            if ($question->getCache()) {
                foreach ($questions as $questionATester) {
                    if ($questionATester->getId() == $question->getVisible()['question_id']) {
                        foreach ($questionATester->getReponses() as $reponse) {
                            if (in_array($reponse->getId(), $question->getVisible()['reponse_id'])
                                && count($reponse->getQuestionnaireReponses()) != 0
                            ) {
                                $question->setCache(false);
                                break;
                            }
                        }

                        break;
                    }
                }
            }
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

        $this->gestionAffichageQuestionCachee($questionnaire1);
        $this->remplacerVariablesDansLibelles($questionnaire1);

        if ($questionnaire2) {
            $this->gestionAffichageQuestionCachee($questionnaire2);
            $this->remplacerVariablesDansLibelles($questionnaire2);
        }

        return array('questionnaire1' => $questionnaire1, 'questionnaire2' => $questionnaire2);
    }
}
