<?php
namespace SceauBundle\Model\Api;


/**
 * Encode un tableau dans une chaine cryptée de l'API
 *
 * <pre>
 * Mohammed 07/10/2015 Création
 * </pre>
 * @author Mohammed
 * @version 1.0
 * @package api
 */
class ApiDecryptFilter
{
      /**
     * La clé de cryptage
     * @var string
     */
    private $passphrase;

    /**
     * Injection des dépendances
     *
     * @param string $psPassphrase La clé de cryptage
     */
    public function __construct($psPassphrase)
    {
        $this->passphrase = $psPassphrase;
    } // __construct

    /**
     * Décode une chaine cryptée de l'API
     *
     * @param string $psData Chaine criptée
     * @return array Tableau de données
     * @throws \InvalidArgumentException
     */
    public function filter($psData)
    {
        // ==== Initialisations ====
        if (substr($psData, 0, 3) != '!1!') {
            throw new \InvalidArgumentException('Crypted content is not correct');
        }
        $liIvSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $lsIv = mcrypt_create_iv($liIvSize, MCRYPT_RAND);
        $liKeysize = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $lsKey = substr($this->passphrase, 0, $liKeysize);

        // ==== Décryptage ====
        $laData = base64_decode(strtr(substr($psData, 3), ',|', '+/'));
        $laData = str_replace("\x0", '', mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $lsKey, $laData, MCRYPT_MODE_ECB, $lsIv));
        $laData = json_decode($laData, true);
        return $laData;
    } // filter
}
