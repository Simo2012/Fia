<?php

namespace FIANET\SceauBundle\Security\Extranet\User;

use FIANET\SceauBundle\Entity\Extranet\Utilisateur;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class WebserviceUserProvider implements UserProviderInterface
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not found.
     *
     * @param string $login The login
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($login)
    {
        // TODO : effectuer un appel au service web ici, récupérer un tableau avec les données de l'utilisateur
        if ($login != 'NONE_PROVIDED') {
            $utilisateurOK = true;
        } else {
            $utilisateurOK = false;
        }

        if ($utilisateurOK) {
            $motDePasse = 'admin';
            $groupes = array('Utilisateur', 'Administrateur');
            $nom = 'Tenaif';
            $prenom = 'Robert';

            $utilisateur = new Utilisateur($login, $motDePasse, null, $groupes, $nom, $prenom);

            //$site = $this->em->getRepository('FIANETSceauBundle:Site')->findOneByNom('Cdiscount');
            $site = $this->em->getRepository('FIANETSceauBundle:Site')
                ->chargerSiteAvecPackageEtOptionsSouscrites('Cdiscount');

            $utilisateur->setSite($site);

            return $utilisateur;
        }

        throw new UsernameNotFoundException(sprintf('L\'utilisateur "%s" n\'existe pas.', $login));
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be totally reloaded (e.g. from the database),
     * or if the UserInterface object can just be merged into some internal array of users / identity map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(
                sprintf('Les instances de "%s" ne sont pas supportées.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'FIANET\SceauBundle\Entity\Extranet\Utilisateur';
    }
}