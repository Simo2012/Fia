<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Flux
 *
 * @ORM\Table(name="Flux")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\FluxRepository")
 */
class Flux
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
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\FluxStatut")
     * @ORM\JoinColumn(name="fluxStatut_id", referencedColumnName="id", nullable=false)
     */
    private $fluxStatut;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Site", inversedBy="flux")
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
}
