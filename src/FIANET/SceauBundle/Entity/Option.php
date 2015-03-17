<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FIANET\SceauBundle\Entity\PackageOption;
use FIANET\SceauBundle\Entity\SiteOption;

/**
 * Option
 *
 * @ORM\Table(name="Option")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\Extranet\OptionRepository")
 */
class Option
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="smallint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptif", type="string", length=255, nullable=true)
     */
    private $descriptif;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;


    /**
     * @var PackageOption
     *
     * @ORM\OneToMany(targetEntity="FIANET\SceauBundle\Entity\PackageOption", mappedBy="option")
     */
    private $packageOptions;
    
    /**
     * @var SiteOption
     *
     * @ORM\OneToMany(targetEntity="FIANET\SceauBundle\Entity\SiteOption", mappedBy="option")
     */
    private $siteOptions;


    public function __construct()
    {
        $this->packageOptions = new ArrayCollection();
        $this->siteOptions = new ArrayCollection();
    }


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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Option
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set descriptif
     *
     * @param string $descriptif
     *
     * @return Option
     */
    public function setDescriptif($descriptif)
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    /**
     * Get descriptif
     *
     * @return string
     */
    public function getDescriptif()
    {
        return $this->descriptif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Option
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Add packageOptions
     *
     * @param PackageOption $packageOptions
     *
     * @return Option
     */
    public function addPackageOption(PackageOption $packageOptions)
    {
        $this->packageOptions[] = $packageOptions;

        return $this;
    }

    /**
     * Remove packageOptions
     *
     * @param PackageOption $packageOptions
     */
    public function removePackageOption(PackageOption $packageOptions)
    {
        $this->packageOptions->removeElement($packageOptions);
    }

    /**
     * Get packageOptions
     *
     * @return PackageOption
     */
    public function getPackageOptions()
    {
        return $this->packageOptions;
    }

    /**
     * Add siteOptions
     *
     * @param SiteOption $siteOptions
     *
     * @return Option
     */
    public function addSiteOption(SiteOption $siteOptions)
    {
        $this->siteOptions[] = $siteOptions;

        return $this;
    }

    /**
     * Remove siteOptions
     *
     * @param SiteOption $siteOptions
     */
    public function removeSiteOption(SiteOption $siteOptions)
    {
        $this->siteOptions->removeElement($siteOptions);
    }

    /**
     * Get siteOptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSiteOptions()
    {
        return $this->siteOptions;
    }
}
