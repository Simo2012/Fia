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
class ApiEncryptFilter
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
     * @param mixed $paData
     *
     * @return string Chaine cryptée
     */
    public function filter($paData)
    {
        // ==== Initialisations ====
        $liIvSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $lsIv = mcrypt_create_iv($liIvSize, MCRYPT_RAND);
        $liKeysize = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $lsKey = substr($this->passphrase, 0, $liKeysize);
        var_dump($paData);
        foreach ($paData as &$lsValue) {
            if (!is_array($lsValue)) {
                $lsValue = utf8_encode($lsValue);
            }
        }

        // ==== Cryptage ====
        $lsCode = json_encode($paData);
        $lsCode = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $lsKey, $lsCode, MCRYPT_MODE_ECB, $lsIv));
        $lsCode = '!1!' . strtr($lsCode, '+/', ',|');
        return $lsCode;
    } // filter
}
