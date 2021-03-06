<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

/**
 * Flux
 *
 * @ORM\Table(name="Flux")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\FluxRepository")
 */
class Flux implements GroupSequenceProviderInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="xml", type="text")
     */
    private $xml;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=32, unique=true)
     */
    private $checksum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateInsertion", type="datetimetz")
     */
    private $dateInsertion;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=39)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="libelleErreur", type="string", length=255, nullable=true)
     */
    private $libelleErreur;


    /**
     * @var FluxStatut
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\FluxStatut")
     * @ORM\JoinColumn(name="fluxStatut_id", referencedColumnName="id", nullable=false)
     */
    private $fluxStatut;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Site", inversedBy="flux")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set xml
     *
     * @param string $xml
     *
     * @return Flux
     */
    public function setXml($xml)
    {
        $this->xml = $xml;

        return $this;
    }

    /**
     * Get xml
     *
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Set checksum
     *
     * @param string $checksum
     * @return Flux
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set dateInsertion
     *
     * @param \DateTime $dateInsertion
     *
     * @return Flux
     */
    public function setDateInsertion($dateInsertion)
    {
        $this->dateInsertion = $dateInsertion;

        return $this;
    }

    /**
     * Get dateInsertion
     *
     * @return \DateTime
     */
    public function getDateInsertion()
    {
        return $this->dateInsertion;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Flux
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set libelleErreur
     *
     * @param string $libelleErreur
     *
     * @return Flux
     */
    public function setLibelleErreur($libelleErreur)
    {
        $this->libelleErreur = $libelleErreur;

        return $this;
    }

    /**
     * Get libelleErreur
     *
     * @return string
     */
    public function getLibelleErreur()
    {
        return $this->libelleErreur;
    }

    /**
     * Set fluxStatut
     *
     * @param FluxStatut $fluxStatut
     *
     * @return Flux
     */
    public function setFluxStatut(FluxStatut $fluxStatut)
    {
        $this->fluxStatut = $fluxStatut;

        return $this;
    }

    /**
     * Get fluxStatut
     *
     * @return FluxStatut
     */
    public function getFluxStatut()
    {
        return $this->fluxStatut;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return Flux
     */
    public function setSite(Site $site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Retourne la séquence des groupes de validations à respecter en fonction de l'état du flux :
     * 1) Vérification du format à la réception.
     * 2) Vérification du contenu à la validation.
     *
     * @return array Tableau ordonné avec les groupes de validation
     */
    public function getGroupSequence()
    {
        if ($this->fluxStatut->getId() == FluxStatut::FLUX_A_TRAITER) {
            $groupSequence = ['reception1', 'reception2', 'reception3', 'reception4', 'reception5', 'reception6',
                'reception7', 'reception8'];

        } else {
            $groupSequence = ['validation'];
        }

        return $groupSequence;
    }
}
