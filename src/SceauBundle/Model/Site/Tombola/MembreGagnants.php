<?php

namespace SceauBundle\Model\Site\Tombola;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
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
class MembreGagnants
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
     * Traducteur de phrases
     * @var Translator
     */
    protected $translator;
    
    /**
    * Constructeur, injection des dépendances
    */
    public function __construct($poManager, $poRequestStack, $poTranslator)
    {
        $this->manager          = $poManager;
        $this->request          = $poRequestStack->getCurrentRequest();
        $this->translator       = $poTranslator;
    } // __construct
    
    
    /***
     * fonction pour recuperer les gangants 
     */
    public function getWinner()
    {
       // $logain = $this->manager->getRepository('SceauBundle:TombolaGain')->find(1);
        $loGagnants = $this->manager->getRepository('SceauBundle:TombolaTirageGagnant')->getGagnants();
        return $loGagnants;
    }
}
