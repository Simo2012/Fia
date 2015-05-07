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

class QuestionnaireRepondu
{
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
        
        $oQuestions = $this->em
                   ->getRepository('FIANETSceauBundle:Question')->getAllQuestionsOrdered($questionnaireType);
        /* ToDo : à revoir pour gestion : sous-questions, questions cachées, questions personnalisées, langues, type de livraison, etc. */
        
        if (!$oQuestions) {
            throw new Exception('Le type de questionnaire n°' . $questionnaireType->getId() . ' ne possède pas de question');
        }
        
        $listeQuestionsReponses = array();
        
        $i = 0;
        
        foreach($oQuestions as $question) {
            $listeQuestionsReponses[$i]['question'] = $question;
            
            /* ToDo : on va ajouter également les réponses dans le tableau */
            $listeQuestionsReponses[$i]['questiontype'] = $question->getQuestionType();
            
            $this->getListeReponses($question, $question->getQuestionType());
            
            $oQuestions = $this->em
                   ->getRepository('FIANETSceauBundle:Question')->getAllQuestionsOrdered($questionnaireType);
            
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
     * @return QuestionnaireReponse[] ou NULL 
     */
    public function getCommentairePrincipal(Questionnaire $questionnaire, QuestionnaireType $questionnaireType) {
        
        $return = NULL;
        
        $commentairePrincipal_id = $questionnaireType->getParametrage()['commentairePrincipal'];
        
        if (is_numeric($commentairePrincipal_id)) {
            $question = $this->em
                   ->getRepository('FIANETSceauBundle:Questionnaire')
                   ->find($commentairePrincipal_id);
            
            if ($question) {
                $questionnaireReponse = $this->em
                       ->getRepository('FIANETSceauBundle:QuestionnaireReponse')
                       ->findOneBy(array('question' => $question, 'questionnaire' => $questionnaire));

                if ($questionnaireReponse) {
                    if ($questionnaireReponse->getCommentaire() && $questionnaireReponse->getCommentaire()!='') {
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
     * @param Question $question Instance de Question
     * @param QuestionType $questionType Instance de QuestionType
     * 
     * @return ... ToDo à compléter
     */
    public function getListeReponses(Question $question, QuestionType $questionType) {
        
        $reponsesInfos = $question->getReponses();
        
        // ToDo à compléter
        
        switch ($questionType->getId()) {
            case 1: // Commentaire
                
            break;
        
            case 2: // Choix unique
            case 4: // Menu déroulant
                
            break;
        
            case 3: // Multichoix
                
            break;
             
            case 5: // Notation
                
            break;
        }
    }
    
    /**
     * Méthode qui permet de récupérer le code libellé de questionnaire à traduire
     * 
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * 
     * @return string le code libellé de questionnaire à traduire ou null
     */
    public function getLibelleQuestionnaireTypeRepondu(QuestionnaireType $questionnaireType) {    
        
        $return = null;
        
        if ($questionnaireType->getParametrage()['libelleQuestionnaireRepondu']) {
            $return = $questionnaireType->getParametrage()['libelleQuestionnaireRepondu'];
        }
        
        return $return;
    }
}
