<?php
namespace FIANET\SceauBundle\Service;

use Doctrine\ORM\EntityManager;
use Exception;
use FIANET\SceauBundle\Entity\Flux;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class GestionFlux
{
    private $em;

    public function __construct(EntityManager $em, RecursiveValidator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * Méthode qui valide un flux lors de sa réception (validation légère).
     *
     * @param integer $site_id Identifiant du site (provenant du paramètre POST SiteID)
     * @param string $xmlInfo XML de la commande (provenant du paramètre POST XMLInfo)
     * @param string $checksum Checksum (provenant du paramètre POST CheckSum)
     * @param string $ip IP du client
     *
     * @return Flux Instance de flux s'il est valide
     *
     * @throws Exception En cas de flux invalide ou si le site n'est pas autorisé à poster
     */
    public function validerReception($site_id, $xmlInfo, $checksum, $ip)
    {
        if (!is_numeric($site_id)) {
            throw new Exception('Le paramètre SiteID est obligatoire.');
        }

        /* TODO : il faudra vérifier que le site ait les garanties Sceau */
        $site = $this->em->getRepository('FIANETSceauBundle:Site')->find($site_id);

        if (!$site) {
            throw new Exception('Site inexistant ou ne disposant pas des droits d\'accès.');
        }

        if ($site->getAdministrationType()->getId() !== 1) {
            throw new Exception('Ce site n\'est pas autorisé à poster des flux.');
        }

        $flux = new Flux();
        $flux->setXml($xmlInfo);
        $flux->setChecksum($checksum);
        $flux->setDateInsertion(new \DateTime());
        $flux->setIp($ip);
        $flux->setFluxStatut($this->em->getRepository('FIANETSceauBundle:FluxStatut')->find(1));
        $flux->setSite($site);

        $listeErreurs = $this->validator->validate($flux, array(), array('reception, reception2'));

        if (count($listeErreurs) == 0) {
            return $flux;
        } else {
            throw new Exception($listeErreurs->get(0)->getMessage());
        }
    }

    /**
     * Méthode qui valide un flux et qui l'insére en base s'il est valide.
     *
     * @param integer $site_id Identifiant du site (provenant du paramètre POST SiteID)
     * @param string $xmlInfo XML de la commande (provenant du paramètre POST XMLInfo)
     * @param string $checksum Checksum (provenant du paramètre POST CheckSum)
     * @param string $ip IP du client
     *
     * @return Flux Instance de flux s'il est valide
     *
     * @throws Exception En cas de flux invalide ou si le site n'est pas autorisé à poster
     */
    public function inserer($site_id, $xmlInfo, $checksum, $ip)
    {
        $flux = $this->validerReception($site_id, $xmlInfo, $checksum, $ip);

        $this->em->persist($flux);
        $this->em->flush();

        return $flux;
    }
}
