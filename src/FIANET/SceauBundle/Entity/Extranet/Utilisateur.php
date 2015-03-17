<?php

namespace FIANET\SceauBundle\Entity\Extranet;

use Doctrine\ORM\Mapping as ORM;
use FIANET\SceauBundle\Entity\Site;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class Utilisateur implements UserInterface
{
    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $motDePasse;

    /**
     * @var string
     */
    private $sel;

    /**
     * @var array
     */
    private $groupes;

    /**
     * @var string
     */
    private $nom;

    /**
     * @var string
     */
    private $prenom;


    /**
     * @var Site
     */
    private $site;


    public function __construct($login, $motDePasse, $sel, $groupes, $nom, $prenom)
    {
        $this->login = $login;
        $this->motDePasse = $motDePasse;
        $this->sel = $sel;
        $this->groupes = $groupes;
        $this->nom = $nom;
        $this->prenom = $prenom;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return Utilisateur
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getMotDePasse()
    {
        return $this->motDePasse;
    }

    /**
     * @param string $motDePasse
     *
     * @return Utilisateur
     */
    public function setMotDePasse($motDePasse)
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    /**
     * @return string
     */
    public function getSel()
    {
        return $this->sel;
    }

    /**
     * @param string $sel
     *
     * @return Utilisateur
     */
    public function setSel($sel)
    {
        $this->sel = $sel;

        return $this;
    }

    /**
     * @return array
     */
    public function getGroupes()
    {
        return $this->groupes;
    }

    /**
     * @param array $groupes
     *
     * @return Utilisateur
     */
    public function setGroupes($groupes)
    {
        $this->groupes = $groupes;

        return $this;
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     *
     * @return Utilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     *
     * @return Utilisateur
     */
    public function setPenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     *
     * @return Utilisateur
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property, and populated in any number of different ways
     * when the user object is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return $this->getGroupes();
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->getMotDePasse();
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->getSel();
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getLogin();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * Vérifie que l'utilisateur passé en argument est identique à cette utilisateur : permet l'authentification.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function equals(UserInterface $user)
    {
        if (!$user instanceof Utilisateur) {
            return false;
        }

        if ($this->motDePasse !== $user->getPassword()) {
            return false;
        }

        if ($this->sel !== $user->getSalt()) {
            return false;
        }

        if ($this->login !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * Retourne le prénom + la première lettre du nom en majuscule suivi d'un point.
     *
     * @return string
     */
    public function getPrenomNomRaccourci()
    {
        return $this->prenom . ' ' . strtoupper(substr($this->nom, 0, 1)) . '.';
    }
}
