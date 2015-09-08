<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Package
 *
 * @ORM\Table(name="Package")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\PackageRepository")
 */
class Package
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
     * @ORM\Column(name="libelle", type="string", length=50)
     */
    private $libelle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;

    /**
     * @var PackageOption
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\PackageOption", mappedBy="package")
     */
    private $packageOptions;


    public function __construct()
    {
        $this->packageOptions = new ArrayCollection();
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
     * @return Package
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
     * Set actif
     *
     * @param boolean $actif
     * @return Package
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
     * @return Package
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPackageOptions()
    {
        return $this->packageOptions;
    }
}
