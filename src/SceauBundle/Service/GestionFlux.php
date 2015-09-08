<?php
namespace SceauBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\AdministrationType;
use SceauBundle\Entity\Flux;
use SceauBundle\Exception\FluxException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GestionFlux
{
    private $em;
    private $validator;
    private $translator;

    public function __construct(ObjectManager $em, ValidatorInterface $validator, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->translator = $translator;
    }

    /**
     * Valide le format d'un flux lors de sa réception.
     *
     * @param integer $site_id Identifiant du site (provenant du paramètre POST SiteID)
     * @param string $xmlInfo XML de la commande (provenant du paramètre POST XMLInfo)
     * @param string $checksum Checksum (provenant du paramètre POST CheckSum)
     * @param string $ip IP du client
     *
     * @return Flux Instance de flux s'il est valide
     *
     * @throws FluxException En cas de flux invalide ou si le site n'est pas autorisé à poster
     */
    private function validerReception($site_id, $xmlInfo, $checksum, $ip)
    {
        if (!is_numeric($site_id)) {
            throw new FluxException($this->translator->trans('flux_siteid_absent', array(), 'flux'));
        }

        /* TODO : il faudra vérifier que le site ait les garanties Sceau */
        $site = $this->em->getRepository('SceauBundle:Site')->find($site_id);

        if (!$site) {
            throw new FluxException($this->translator->trans('flux_site_inexistant_ou_sans_garantie', array(), 'flux'));
        }

        if ($site->getAdministrationType()->getId() !== AdministrationType::FLUX_XML) {
            throw new FluxException($this->translator->trans('flux_site_non_autorise', array(), 'flux'));
        }

        $flux = new Flux();
        $flux->setXml($xmlInfo);
        $flux->setChecksum($checksum);
        $flux->setDateInsertion(new \DateTime());
        $flux->setIp($ip);
        $flux->setFluxStatut($this->em->getRepository('SceauBundle:FluxStatut')->aTraiter());
        $flux->setSite($site);

        $listeErreurs = $this->validator->validate($flux);

        if (count($listeErreurs) == 0) {
            return $flux;
        } else {
            throw new FluxException($listeErreurs->get(0)->getMessage());
        }
    }

    /**
     * Valide le format d'un flux lors de sa réception et l'insére en base s'il est valide.
     *
     * @param integer $site_id Identifiant du site (provenant du paramètre POST SiteID)
     * @param string $xmlInfo XML de la commande (provenant du paramètre POST XMLInfo)
     * @param string $checksum Checksum (provenant du paramètre POST CheckSum)
     * @param string $ip IP du client
     *
     * @throws FluxException En cas de flux invalide ou si le site n'est pas autorisé à poster
     */
    public function inserer($site_id, $xmlInfo, $checksum, $ip)
    {
        $flux = $this->validerReception($site_id, $xmlInfo, $checksum, $ip);

        $this->em->persist($flux);
        $this->em->flush();
    }

    /**
     * Valide le contenu d'un flux XML. Lance une exception s'il est invalide.
     *
     * @param Flux $flux Instance de flux
     *
     * @throws FluxException En cas de flux invalide
     */
    public function validerContenu(Flux $flux)
    {
        $listeErreurs = $this->validator->validate($flux);

        if (count($listeErreurs) != 0) {
            throw new FluxException($listeErreurs->get(0)->getMessage());
        }
    }

    /**
     * Permet de récupérer la valeur que doit avoir la balise <crypt> pour un flux.
     *
     * @param string $clePriveeSceau Clé privée Sceau du site
     * @param string $refid Valeur de la balise <refid>
     * @param string $timestamp Valeur de l'attibut timestamp de la balise <ip>
     * @param string $email Valeur de la balise <email>
     *
     * @return string La valeur attendue pour la balise crypt
     */
    public function getCrypt($clePriveeSceau, $refid, $timestamp, $email)
    {
        return md5($clePriveeSceau . '_' . $refid . '+' . $timestamp . '=' . $email);
    }
}
