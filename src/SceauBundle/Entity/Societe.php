<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Societe
 *
 * @ORM\Table(name="Societe")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\SocieteRepository")
 */
class Societe
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
     * @ORM\Column(name="libelle", type="string", length=100)
     */
    private $libelle;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Site", mappedBy="societe")
     **/
    private $sites;


    public function __construct()
    {
        $this->sites = new ArrayCollection();
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
     * @return Societe
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
     * Add sites
     *
     * @param Site $site
     *
     * @return Societe
     */
    public function addSite(Site $site)
    {
        $this->sites[] = $site;

        return $this;
    }

    /**
     * Remove sites
     *
     * @param Site $site
     */
    public function removeSite(Site $site)
    {
        $this->sites->removeElement($site);
    }

    /**
     * Get sites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSites()
    {
        return $this->sites;
    }
}
