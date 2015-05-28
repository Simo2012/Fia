<?php

namespace FIANET\SceauBundle\Service;

use Doctrine\ORM\EntityManager;
use FIANET\SceauBundle\Entity\Question;
use FIANET\SceauBundle\Entity\Questionnaire;
use FIANET\SceauBundle\Entity\QuestionnaireReponse;
use FIANET\SceauBundle\Entity\QuestionnaireType;
use FIANET\SceauBundle\Entity\QuestionType;
use FIANET\SceauBundle\Entity\Site;
use Symfony\Component\Config\Definition\Exception\Exception;
use \DateTime;

class QuestionnaireRepondu {

    private $em;

    public function __construct(
    EntityManager $em
    ) {
        $this->em = $em;
    }

    /**
     * Méthode qui permet de savoir si un Questionnaire existe bien et qu'il est actif
     * 
     * @param integer $questionnaire_id Identifiant du Questionnaire
     * 
     * @return Boolean true s'il existe bien et qu'il est actif
     */
    public function verifierValiditeQuestionnaire($questionnaire_id) {

        $return = false;

        if (is_numeric($questionnaire_id)) {
            $questionnaire = $this->em
                    ->getRepository('FIANETSceauBundle:Questionnaire')
                    ->find($questionnaire_id);

            if ($questionnaire) {
                if ($questionnaire->getActif()) {
                    $return = true;
                }
            }
        }

        return $return;
    }

    /**
     * Méthode qui permet de savoir si un QuestionnaireReponse existe bien
     * 
     * @param integer $questionnaireReponse_id Identifiant du QuestionnaireReponse
     * 
     * @return Boolean true s'il existe bien
     */
    public function verifierValiditeQuestionnaireReponse($questionnaireReponse_id) {

        $return = false;

        if (is_numeric($questionnaireReponse_id)) {
            $questionnaireReponse = $this->em
                    ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                    ->find($questionnaireReponse_id);

            if ($questionnaireReponse) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * Méthode qui permet de savoir si un QuestionnaireReponse donné est bien lié à un Questionnaire donné
     * 
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * @param QuestionnaireReponse $questionnaireReponse Instance de QuestionnaireReponse
     * 
     * @return Boolean true s'il y a cohérence
     */
    public function coherenceQuestionnaireVsQuestionnaireReponse(Questionnaire $questionnaire, QuestionnaireReponse $questionnaireReponse) {

        $return = false;

        if ($questionnaire->getQuestionnaireReponses()) {
            if ($questionnaire->getQuestionnaireReponses()->contains($questionnaireReponse)) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * Méthode qui permet de savoir si un Questionnaire donné est bien lié à un Site donné
     * 
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * @param Site $site Instance de Site
     * 
     * @return Boolean true s'il y a cohérence
     */
    public function coherenceQuestionnaireVsSite(Questionnaire $questionnaire, Site $site) {

        $return = ($questionnaire->getSite() == $site) ? true : false;
        return $return;
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
    public function coherenceArgumentsDroitDeReponse(Site $site, $questionnaire_id, $questionnaireReponse_id) {

        $return = false;

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
                        return true;
                    }
                }
            }
        }

        return $return;
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
    public function coherenceArgumentsDroitDeReponseAjout(Site $site, $questionnaire_id, $questionnaireReponse_id) {

        $return = false;

        if ($this->coherenceArgumentsDroitDeReponse($site, $questionnaire_id, $questionnaireReponse_id)) {

            $questionnaireReponse = $this->em
                    ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                    ->find($questionnaireReponse_id);

            // S'il n'existe pas déjà un droit de réponse actif pour le questionnaireReponse alors on pourra ajouter un droit de réponse
            $nb_DroitDeReponse_Actif = $this->em
                    ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                    ->nbDroitDeReponseActif($questionnaireReponse);
            if ($nb_DroitDeReponse_Actif == 0) {
                return true;
            }
        }

        return $return;
    }

    /**
     * Méthode qui permet de savoir si les arguments donnés pour l'appel d'affichage des détails d'un questionnaire sont cohérents
     * 
     * @param Site $site Instance de Site
     * @param integer $questionnaire_id Identifiant du Questionnaire
     * 
     * @return Boolean true s'il y a cohérence
     */
    public function coherenceArgumentsDetailsQuestionnaire(Site $site, $questionnaire_id) {

        $return = false;

        if ($this->verifierValiditeQuestionnaire($questionnaire_id)) {

            $questionnaire = $this->em
                    ->getRepository('FIANETSceauBundle:Questionnaire')
                    ->find($questionnaire_id);

            if ($this->coherenceQuestionnaireVsSite($questionnaire, $site)) {
                return true;
            }
        }
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
    public function getAllQuestionsReponses(Questionnaire $questionnaire, QuestionnaireType $questionnaireType) {

        $oQuestions = $this->em->getRepository('FIANETSceauBundle:Question')->getAllQuestionsOrdered($questionnaireType);

        if (!$oQuestions) { throw new Exception('Le type de questionnaire n°' . $questionnaireType->getId() . ' ne possède pas de question'); }

        $listeQuestionsReponses = array();
        
        $i = 0;
        
        foreach ($oQuestions as $question) {
            $listeQuestionsReponses[$i]['question'] = $question;
            $listeQuestionsReponses[$i]['questionType'] = $question->getQuestionType();
            
            $commentairePrincipal = isset($questionnaireType->getParametrage()['commentairePrincipal']) ? $questionnaireType->getParametrage()['commentairePrincipal'] : null;
            
            $listeQuestionsReponses[$i]['commentairePrincipal'] = ($commentairePrincipal != null && $question->getId() == $commentairePrincipal) ? true : false;         
            
            $listeQuestionsReponses[$i]['questionPrimaire'] = $question->getQuestionPrimaire();
            $listeQuestionsReponses[$i]['questionsSecondaires'] = $question->getQuestionsSecondaires();            
            
            $listeQuestionsReponses[$i]['cacher'] = false;
            
            $influenceFianet = isset($questionnaireType->getParametrage()['influenceFianet']) ? $questionnaireType->getParametrage()['influenceFianet'] : null;

            /* ToDo : condition à revoir car la question sur l'influence de FIA-NET devrait peut-être être visible sur d'autres interfaces (à voir selon les specs des autres lots...) */
            if ($influenceFianet != null && $question->getId() == $influenceFianet) {
                $listeQuestionsReponses[$i]['cacher'] = true;
            } else {
            
                $listeQuestionsReponses[$i]['reponses'] = $this->getListeReponses($questionnaire,$question);
                
                /* Si la question n'est pas cachée par défaut et que l'internaute n'y a pas répondu; on indique le message "Pas de réponse à cette question" */
                if ($question->getCache() == false && $listeQuestionsReponses[$i]['reponses'] == null) {
                    $listeQuestionsReponses[$i]['questionRepondue'] = false;
                } else {
                    $listeQuestionsReponses[$i]['questionRepondue'] = true;
                    
                    if ($question->getCache() == true) {
                        $listeQuestionsReponses[$i]['cacher'] = true;
                    }

                }
                
            }
            
            $i++;
        }

        return $listeQuestionsReponses;
    }

    /**
     * Méthode qui permet de récupérer le commentaire principal dans le cas où il existe
     * 
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * 
     * @return QuestionnaireReponse[]|null 
     */
    public function getCommentairePrincipal(Questionnaire $questionnaire, QuestionnaireType $questionnaireType) {

        $return = null;
        $commentairePrincipal_id = null;

        if (isset($questionnaireType->getParametrage()['commentairePrincipal'])) {
            $commentairePrincipal_id = $questionnaireType->getParametrage()['commentairePrincipal'];
        }

        if (is_numeric($commentairePrincipal_id)) {
            $question = $this->em
                    ->getRepository('FIANETSceauBundle:Questionnaire')
                    ->find($commentairePrincipal_id);

            if ($question) {
                $questionnaireReponse = $this->em
                        ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                        ->findOneBy(array('question' => $question, 'questionnaire' => $questionnaire));

                if ($questionnaireReponse) {
                    if ($questionnaireReponse->getCommentaire() && $questionnaireReponse->getCommentaire() != '') {
                        $return = $questionnaireReponse;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Méthode qui permet de récupérer les réponses répondues pour une question donnée
     * 
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * @param Question $question Instance de Question
     * 
     * @return $oReponsesRepondues|null Array Collection de QuestionnaireReponse ou null si aucune réponse n'est trouvée (hors cas questionType notation)
     */
    public function getListeReponses(Questionnaire $questionnaire, Question $question) {

        $questionType  = $question->getQuestionType();
               
        switch ($questionType->getId()) {

            case 5: // Notation
                /* on affichera toutes les réponses proposées, suivies des éventuelles réponses données ou NC (N/A) si pas de réponse */
                $oReponsesProposees = $question->getReponses();
                $oReponsesRepondues = $this->em
                    ->getRepository('FIANETSceauBundle:QuestionnaireReponse')->getAllReponsesRepondues($question, $questionnaire);
                /* ToDo : lignes ci-dessus à modifier ! */
                
            break;
        
            default:
                $oReponsesRepondues = $this->em
                    ->getRepository('FIANETSceauBundle:QuestionnaireReponse')->getAllReponsesRepondues($question, $questionnaire);
            break;
        
        }
        
        return $oReponsesRepondues;
    }

    /**
     * Méthode qui permet de récupérer le code libellé de questionnaire à traduire
     * 
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * 
     * @return string|null le code libellé de questionnaire à traduire, ou null si le code libellé n'existe pas
     */
    public function getLibelleQuestionnaireTypeRepondu(QuestionnaireType $questionnaireType) {

        $return = null;

        if (isset($questionnaireType->getParametrage()['libelleQuestionnaireRepondu'])) {
            $return = $questionnaireType->getParametrage()['libelleQuestionnaireRepondu'];
        }
        
        return $return;
    }

    /**
     * Méthode qui permet de récupérer le questionnaire principal à afficher
     * Si un Q2 est en paramètre, on retournera le Q1.
     * Si un QU est en paramètre, on retournera ce QU.
     * 
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * 
     * @return Questionnaire[]
     */
    public function getQuestionnairePrincipal(Questionnaire $questionnaire) {

        if ($questionnaire->getQuestionnaireLie()) {
            $questionnaire = $questionnaire->getQuestionnaireLie();
        }

        return $questionnaire;
    }

    /**
     * Méthode qui permet de récupérer le questionnaire lié suivant à afficher
     * 
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * 
     * @return Questionnaire[]
     */
    public function getQuestionnaireLieSuivant(Questionnaire $questionnaire) {

        $questionnaireTypeSuivant = $questionnaire->getQuestionnaireType()->getQuestionnaireTypeSuivant();

        $questionnaireLieSuivant = $this->em
                ->getRepository('FIANETSceauBundle:Questionnaire')
                ->findOneBy(array('questionnaireLie' => $questionnaire, 'questionnaireType' => $questionnaireTypeSuivant));

        return $questionnaireLieSuivant;
    }
    
    /**
     * Méthode qui permet de créer le message à afficher lorsqu'un questionnaire lié suivant n'est pas envoyé/répondu
     * 
     * @param Questionnaire $questionnaire Instance de Questionnaire
     * 
     * @return array Tableau contenant le code message à traduire et à afficher + la date d'envoi prévu ou effectué
     */
    public function getMsgQuestionnaireLieSuivant(Questionnaire $questionnaire) {

        $questionnaireLieSuivantMsg = array();

        if ($questionnaire->getDateEnvoi()) {

            $questionnaireLieSuivantMsg['dateEnvoi'] = $questionnaire->getDateEnvoi();

            $nbJoursPourRepondre = $this->getQuestionnairePrincipal($questionnaire)->getQuestionnaireType()->getNbJoursPourRepondre();

            $dateDuJour = new DateTime();
            $dateDiff = $dateDuJour->diff($questionnaireLieSuivantMsg['dateEnvoi']);
            $nbJoursPasses = $dateDiff->format('%a');

            if ($nbJoursPasses <= $nbJoursPourRepondre) {
                $questionnaireLieSuivantMsg['texte'] = 'questionnaire_envoye_non_repondu';
            } else {
                $questionnaireLieSuivantMsg['texte'] = 'questionnaire_envoye_delai_depasse';
            }
        } else {
            $questionnaireLieSuivantMsg['dateEnvoi'] = $questionnaire->getDatePrevEnvoi();
            $questionnaireLieSuivantMsg['texte'] = 'questionnaire_non_envoye';
        }

        return $questionnaireLieSuivantMsg;
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
        
        $navigation['precedent'] = $this->em->getRepository('FIANETSceauBundle:Questionnaire')->getQuestionnaireReponduNavigation(
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
        
        $navigation['suivant'] = $this->em->getRepository('FIANETSceauBundle:Questionnaire')->getQuestionnaireReponduNavigation(
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

}
