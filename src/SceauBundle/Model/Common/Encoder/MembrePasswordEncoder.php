<?php

namespace SceauBundle\Model\Common\Encoder;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * Gérer L'encodage et décodage des mots de passe
 *
 * <pre>
 * Mohammed 07/10/2015 Création
 * </pre>
 * @author Mohammed
 * @version 1.0
 * @package Sceau
 */
class MembrePasswordEncoder  implements PasswordEncoderInterface
{
    /**
     * Modèle d'encryptage d'une chaine de caractère
     * @var $apiEncryptFilter
     */
    private $apiEncryptFilter;

    /**
     * Contructeur
     * @param $apiEncryptFilter
     */
    public function __construct($apiEncryptFilter)
    {
        $this->apiEncryptFilter = $apiEncryptFilter;
    }

    /**
     * Encode le password
     * @param string $raw
     * @param string $salt
     * @return string
     */
    public function encodePassword($raw, $salt)
    {
        // ==== Vérifie la longueur du mot de passe ====
        if (!$this->checkPasswordLength($raw)) {
            return false;
        }

        // ==== Encodage ====
        return $this->apiEncryptFilter->filter(array($raw));
    }

    /**
     * Vérifie la validité du password
     * @param string $encoded
     * @param string $raw
     * @param string $salt
     * @return bool|void
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        // ==== Vérifie la longueur du mot de passe ====
        if (!$this->checkPasswordLength($raw)) {
            return false;
        }

        // ==== Vérification ====
        if ($encoded != $raw) {
            return true;
        }

        return false;
    }

    /**
     * Test la longueur du mot de passe
     * @param $raw
     * @return bool
     */
    public function checkPasswordLength($raw)
    {
        if (strlen($raw) <= 12) {
            return true;
        }

        return false;
    }

}
