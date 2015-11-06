<?php
namespace SceauBundle\Model\Site\User;

use SceauBundle\Entity\Membre;
use SceauBundle\Entity\TombolaTicket;
use Doctrine\ORM\EntityManager;
use SceauBundle\Model\Api\ApiDecryptFilter;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use SceauBundle\Entity\Coordonnee;
use SceauBundle\Entity\Email;
use SceauBundle\Entity\Avatar;

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
        $this->manager = $poManager;
        $this->factory = $poFactory;
        $this->request = $poRequestStack->getCurrentRequest();
        $this->translator = $poTranslator;
        $this->apiDecryptFilter = $apiDecryptFilter;
    }
// __construct

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

        $loEmail = $this->manager->getRepository('SceauBundle:Email')->getDateConfirmation($poLogin);
        if ($loEmail->getDateConfirmation() == null) {
            throw new \ErrorException(
                'Email non Confirmé'
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
    }// getUser

    /**
     * Récupère un utilisateur par son email et le connect
     *
     * @return User
     */
    public function logConfirmationUser($poLogin)
    {

        $loEmail = $this->manager->getRepository('SceauBundle:Email')->getDateConfirmation($poLogin);
        $loEmail->setDateConfirmation(new \DateTime());
        $this->manager->flush();
        return $loEmail;
    }// getUser

    /**
     * Fonction pour afficher le mot de passe en clair
     * @param type $poUser
     * @return type
     */
    public function displayPassword($poUser)
    {
        $laPassword = $this->apiDecryptFilter->filter($poUser->getPassword());
        return $laPassword[0];
    }

    /**
     * Fonction pour afficher le mot de passe en clair
     * @param type $poPwd
     * @return type
     */
    public function affPassword($poPwd)
    {
        $laPassword = $this->apiDecryptFilter->filter($poPwd);
        return $laPassword[0];
    }

    /**
     * Log l'utilisateur
     *
     * @param Membre $poMembre Utilisateur
     */
    public function setUserInSession(Membre $poMembre)
    {
        $poMembre->setDateCreation(new \DateTime());
        $this->manager->flush();
    }// setUserInSession

    /**
     * Enregistre l'utilisateur et le log
     *
     * @param User $poUser Utilisateur
     * @param Boolean $pbCheckEmail Verification ou non de l'email
     * @return User
     * @throws \ErrorException
     */
    public function registerUser($poUser, $loMail, $pbCheckEmail = true)
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
        $loUser = $this->manager->getRepository('SceauBundle:Membre')->getByPseudo($poUser->getPseudo());
        if (!empty($loUser)) {
            throw new \ErrorException(
                'Pseudo Exist déja'
            );
        }


        $this->createUser($poUser);

        // ==== Mise en session de l'utilisateur ====
        $this->setUserInSession($poUser);

        return $poUser;
    }// registerUser

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
        } catch (\Exception $e) {
            throw new \ErrorException(
                'erreur lors de la creation user'
            );
        }
    }// createUser

    /**
     * Participation du membre dans tombola
     *
     * @param $poUser
     * @param $poSourceId
     * @$return Membre
     * @throws DBALException
     */
    public function particpateTombola($poUser, $poSourceId)
    {
        $loTombolaTicket = new TombolaTicket();
        try {
            $loTombolaTicket->setDateInsert(new \DateTime());
            $loTombolaTicket->setDateParticipe(new \DateTime());
            $loTombolaTicket->setMembre($poUser);

            $loSource = $this->manager->getRepository('SceauBundle:TombolaSource')->find($poSourceId);

            $loTombolaTicket->setTombolaSource($loSource);
            $this->manager->persist($loTombolaTicket);
            $this->manager->flush();
        } catch (\Exception $e) {
            throw new \ErrorException(
                'erreur lors de creation du nouveau ticket tombolat'
            );
        }
        return $loTombolaTicket->getId();
    }

    /**
     * Sauvgarder Coordonnées
     *
     * @param $poField
     * @$return Coordonnees
     * @throws DBALException
     */
    public function saveCoordonnees($poField)
    {
        $loPays = $this->manager->getRepository('SceauBundle:Pays')->find($poField['pays']);
        $loCoordonnees = new Coordonnee();
        try {
            $loCoordonnees->setAdresse($poField['adresse']);
            $loCoordonnees->setCodePostal($poField['codePostal']);
            $loCoordonnees->setCompAdresse($poField['compAdresse']);
            $loCoordonnees->setVille($poField['ville']);
            $loCoordonnees->setPays($loPays);
            $this->manager->persist($loCoordonnees);
            $this->manager->flush();
        } catch (\Exception $e) {
            throw new \ErrorException(
                'erreur lors de creation des Coordonnées'
            );
        }

        return $loCoordonnees;
    }

    /**
     * Sauvgarder Email
     *
     * @param $poField
     * @$return Email
     * @throws DBALException
     */
    public function saveEmail($poField)
    {
        $loEmail = new Email();
        $loUser = $this->manager->getRepository('SceauBundle:Membre')->getByMail($poField['email']['first']);
        if (!empty($loUser)) {
            throw new \ErrorException(
                'Email Exist déja'
            );
        }
        try {
            $loEmail->setEmail($poField['email']['first']);
            $loEmail->setPrincipal(true);
            //$loEmail->setDateConfirmation(new \DateTime());
            $this->manager->persist($loEmail);
            $this->manager->flush();
        } catch (\Exception $e) {
            throw new \ErrorException(
                'erreur lors de creation des Emails'
            );
        }
        return $loEmail;
    }

    public function saveAvatar($poIdAvatar)
    {
        if (is_numeric($poIdAvatar)) {
            $loAvatar = new Avatar();
            try {
                $loAvatar->setNumber($poIdAvatar);
                $this->manager->persist($loAvatar);
                $this->manager->flush();
            } catch (Exception $ex) {
                throw new \ErrorException(
                    'erreur lors de creation d\'avatar'
                );
            }
            return $loAvatar;
        }
        return null;
    }

    /**
     * @param type $poUser
     * @return int
     */
    public function getPourcentage($poUser)
    {
        // Bar de pourcentage
        $loPercentage = 61;

        if ($poUser->getNewsletter() == true) {
            $loPercentage += 3;
        }
        if ($poUser->getCoordonnee() != null) {
            if (!$poUser->getCoordonnee()->getTelephoneFixe() != null) {
                $loPercentage += 3;
            }
            if ($poUser->getCoordonnee()->getTelephoneMobile() != null) {
                $loPercentage += 3;
            }
            if ($poUser->getCoordonnee()->getAdresse() != null) {
                $loPercentage += 3;
            }
            if ($poUser->getCoordonnee()->getPays() != null) {
                if ($poUser->getCoordonnee()->getPays()->getLibelle() != 'France') {
                    $loPercentage += 9;
                }
            }
        }
        if ($poUser->getPseudo() != null) {
            $loPercentage += 3;
        }
        if ($poUser->getAvatar() != null) {
            $loPercentage += 3;
        }
        if ($poUser->getPreference() != null) {
            $loPercentage += 3;
        }
        if ($poUser->getSituationFamiliale() != null &&
            $poUser->getActiviteProfessionnelle() != null ||
            $poUser->getTrancheAge() != null
        ) {
            $loPercentage += 3;
        }
        return $loPercentage;
    }

    /**
     *
     * @param type $poEmail
     * @return Email
     * @throws \ErrorException
     */
    public function saveEmailSecondaire($poEmail)
    {
        $loEmail = $this->manager->getRepository('SceauBundle:Email')->findBy(array('email' => $poEmail));
        if (!empty($loEmail)) {
            throw new \ErrorException(
                'Email Exist déja'
            );
        }
        $loEmail = new Email();
        try {
            $loEmail->setEmail($poEmail);
            $loEmail->setPrincipal(false);
            $this->manager->persist($loEmail);
            $this->manager->flush();
        } catch (\Exception $e) {
            throw new \ErrorException(
                'erreur lors de creation des Emails'
            );
        }
        return $loEmail;
    }

    /**
     *
     * @param type $poEmail
     * @param type $poUser
     * @throws \ErrorException
     */
    public function saveEmailPrincipale($poEmail, $poUser)
    {
        $loEmail = $this->manager->getRepository('SceauBundle:Email')->findOneBy(array('email' => $poEmail));
        if (!empty($loEmail)) {
            if ($this->checkEmailExist($poUser, $loEmail) == false) {
                throw new \ErrorException(
                    'Email Exist déja'
                );
            } else {
                $loEmail->setPrincipal(true);
            }
        } else {
            $loEmail = new Email();
            try {
                $loEmail->setEmail($poEmail);
                $loEmail->setPrincipal(true);
                $this->manager->persist($loEmail);
                $poUser->addEmail($loEmail);
            } catch (\Exception $e) {
                throw new \ErrorException(
                    'erreur lors de creation des Emails'
                );
            }
        }
        $this->manager->flush();
    }

    /**
     *
     * @param type $poUser
     * @param type $poEmail
     * @return boolean
     */
    public function checkEmailExist($poUser, $poEmail)
    {

        foreach ($poUser->getEmails() as $loEmail) {
            if ($poEmail->getEmail() == $loEmail->getEmail()) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param type $poUser
     * @param type $poPwd
     * @return boolean
     */
    public function checkpwd($poUser, $poPwd)
    {
        $laPassword = $this->apiDecryptFilter->filter($poUser->getPassword());
        $lsPassword = $laPassword[0];
        if ($poPwd !== $lsPassword) {
            return false;
        }
        return true;
    }

    /**
     * @param type $poUser
     * @param type $poField
     * @throws \ErrorException
     */
    public function updatePwd($poUser, $poField)
    {
        if ($this->checkpwd($poUser, $poField['password_actuel']) == false) {
            throw new \ErrorException(
                'Password Actuel Incorrect'
            );
        } else {
            if ($poField['password_nouveau'] != $poField['conf_password_nouveau']) {
                throw new \ErrorException(
                    'Les Mots de passes sont différents'
                );
            } else {
                $loEncoder = $this->factory->getEncoder($poUser);
                $lsPassword = $loEncoder->encodePassword($poField['password_nouveau'], $poUser->getSalt());
                $poUser->setPassword($lsPassword);
                $this->manager->flush($poUser);
            }
        }
    }

    /**
     * @param type $poUser
     * @return type
     */
    public function getPreferences($poUser)
    {
        $loPreference = $poUser->getPreference();
        $laPreference = null;
        for ($i = 0; $i < strlen($loPreference); $i++) {
            if ($loPreference[$i] != '{' &&
                $loPreference[$i] != '}' &&
                $loPreference[$i] != ','
            ) {
                $laPreference[] = $loPreference[$i];
            }
        }
        return $laPreference;
    }

    public function savePreference($poUser, $paPreference)
    {
        $loPreference = '{';
        $count = count($paPreference);
        $pos = 1;
        foreach ($paPreference as $preference) {
            $loPreference .= $preference;
            if ($pos != $count) {
                $loPreference .= ',';
            }
            $pos++;
        }
        $loPreference .= '}';
        $poUser->setPreference($loPreference);
        $this->manager->flush($poUser);
    }
}
