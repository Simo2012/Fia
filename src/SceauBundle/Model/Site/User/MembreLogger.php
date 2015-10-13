<?php

namespace SceauBundle\Model\Site\User;

use SceauBundle\Entity\Membre;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
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
class MembreLogger
{
    /**
    * Doctrine entity manager
    * @var EntityManager
    */
    private $manager;
    
    /**
     * Modèle de décodage des mots de passe encodés via l'encodeur
     * utilisant le modèle d'encodage ApiEncryptFilter
     * @var ApiDecryptFilter
     */
    private $apiDecryptFilter;
    
    /**
    * Gestionnaire d'encodeurs
    * @var EncoderFactory
    */
    private $factory;
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
    public function __construct($poManager, $poFactory, $poRequestStack, $poTranslator, $apiDecryptFilter)
    {
        $this->manager          = $poManager;
        $this->factory          = $poFactory;
        $this->request          = $poRequestStack->getCurrentRequest();
        $this->translator       = $poTranslator;
        $this->apiDecryptFilter = $apiDecryptFilter;
    } // __construct
    
    
    /**
     * Récupère un utilisateur par son email et le connect
     *
     * @return User
     */
    public function logUser($poLogin, $poPassword)
    {
        // ==== Lecture d'un utilisateur ====
        $loUser = $this->manager->getRepository('SceauBundle:Membre')->getByMail($poLogin);
        if ($loUser === null) {
            throw new AuthenticationCredentialsNotFoundException(
                'Incorrect Login'
            );
        }
        $laPassword = $this->apiDecryptFilter->filter($loUser->getPassword());
        $lsPassword = $laPassword[0];
        if ($poPassword !== $lsPassword) {
            throw new BadCredentialsException(
                'Incorrect Password'
            );
        }
        // ==== Mise en session de l'utilisateur ====
        $this->setUserInSession($loUser);
        return $loUser;
    } // getUser
    
    /**
    * Log l'utilisateur
    *
    * @param Membre $poMembre Utilisateur
    */
    public function setUserInSession(Membre $poMembre)
    {
        // ==== Mise en session du token d'accès sécurisé ====
        $loToken = new UsernamePasswordToken(
            $poMembre,
            $poMembre->getPassword(),
            'secured_users_area',
            $poMembre->getRoles()
        );
        $loSession = $this->request->getSession();
        $loSession->set('_security_secured_users_area', serialize($loToken));

        // ==== Enregistrement de la date de login ====
        $poMembre->setDateCreation(new \DateTime());
        $this->manager->flush();
    } // setUserInSession
    
    
    /**
    * Enregistre l'utilisateur et le log
    *
    * @param User $poUser Utilisateur
    * @param Boolean $pbCheckEmail Verification ou non de l'email
    * @return User
    * @throws \ErrorException
    */
    public function registerUser($poUser,$loMail,$pbCheckEmail = true)
    {
        // ==== Création de l'utilisateur ====
        //$lsEmail = $poUser->get;
        if ($pbCheckEmail) {
            $loUser = $this->manager->getRepository('SceauBundle:Membre')->getByMail($loMail);
            if (!empty($loUser)) {
                throw new \ErrorException(
                    'Email Exist déja'
                );
            }
        }
        $this->createUser($poUser);

        // ==== Mise en session de l'utilisateur ====
        $this->setUserInSession($poUser);

        return $poUser;
    } // registerUser
    
    
    /**
     * Création d'un nouvel utilisateur
     *
     * @param $poUser
     * @return Membre
     * @throws DBALException
     */
    public function createUser($poUser)
    {
        $loEncoder = $this->factory->getEncoder($poUser);
        $lsPassword = $loEncoder->encodePassword($poUser->getPassword(), $poUser->getSalt());
        
        $poUser->setPassword($lsPassword);
        $poUser->setDateCreation(new \DateTime());
        try {
            // ---- Mise en session de la 1ere inscription pour la popup de bienvenue ----
            $loSession = $this->request->getSession();
            $loSession->set('_is_first_registration', true);        
            $this->manager->persist($poUser);
            $this->manager->flush();
        } catch(\Exception $e) {
            var_dump('erreur lors de la creation user');
        }
    } // createUser
    

}