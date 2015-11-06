<?php
namespace SceauBundle\Model\Site\Home;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Connexion des utilisateurs
 *
 * <pre>
 * Mohammed 11/02/2015 Création
 * </pre>
 * @author Mohammed
 * @version 1.0
 * @package Sceau
 */
class PreniumSite
{
     /**
    * Doctrine entity manager
    * @var EntityManager
    */
    private $manager;
    
    /**
     * Requête courante
     * @var Request
     */
    private $request;
    
    /**
    * Constructeur, injection des dépendances
    */
    public function __construct($poManager, $poRequestStack)
    {
        $this->manager          = $poManager;
        $this->request          = $poRequestStack->getCurrentRequest();
    } // __construct
    
    /**
    * Recuperer Prenium Site
    */
    public function getPreniumSite()
    {
        $lpSitePrenuim = null;
        $loPrenium = $this->manager->getRepository('SceauBundle:Site')->getPreniumSite();

        for ($j=0; $j<count($loPrenium); $j++) {
            $loSitePrenium = $loPrenium[$j];
            $lpSitePrenuim['sitePrenium'][$j] = $loPrenium[$j];
         
            $loQuestionnaireType = $loSitePrenium['questionnairePersonnalisations'][0]['questionnaireType'];
            $loParametrage = $loQuestionnaireType['parametrage'];
            
            if ($loParametrage['indicateur']['type'] ===  'reponse_id') {
                $loVert =  $loParametrage['indicateur']['vert'];
                $loQuestionid = $loParametrage['indicateur']['question_id'];
                $loCommentairePrincipale = $loParametrage['commentairePrincipal'];
                $loQuestionRepondu = $loParametrage['libelleQuestionnaireRepondu'];
                $loIdQuestionnaire = eregi_replace("[^0-9]", "", $loQuestionRepondu); // TODO : deprecated
                $lsResponseId = $this->manager->getRepository('SceauBundle:QuestionnaireReponse')
                                     ->getComment($loCommentairePrincipale, $loIdQuestionnaire, $loQuestionid, $loVert);
                $lpSitePrenuim['sitePrenium'][$j]['information'] =  $lsResponseId;
            } else {
                //traitement en cas de note
            }
        }
        
        return $lpSitePrenuim;
    }
    
    /**
     * Remplir un tableau par 4 valeurs random
     */
    public function getRandom()
    {
        $loCount =  $this->manager->getRepository('SceauBundle:Site')->getCount();
        $laRand = [];
        $i = 0;
        while ($i < 4) {
            $loRand  = rand(1, $loCount);
            if (!in_array($loRand, $laRand)) {
                $laRand[] = $loRand;
                $i++;
            }
        }
       
        return $laRand;
    }
}
