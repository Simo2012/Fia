<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SiteCategorie
 *
 * @ORM\Table(name="SiteCategorie")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\SiteCategorieRepository")
 */
class SiteCategorie
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
     * @var boolean
     *
     * @ORM\Column(name="principal", type="boolean")
     */
    private $principal;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Site", inversedBy="siteCategories")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     **/
    private $site;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Categorie", inversedBy="siteCategories")
     * @ORM\JoinColumn(name="categorie_id", referencedColumnName="id", nullable=false)
     **/
    private $categorie;


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
     * Set principal
     *
     * @param boolean $principal
     *
     * @return SiteCategorie
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;

        return $this;
    }

    /**
     * Get principal
     *
     * @return boolean
     */
    public function getPrincipal()
    {
        return $this->principal;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return SiteCategorie
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
     * Set categorie
     *
     * @param Categorie $categorie
     *
     * @return SiteCategorie
     */
    public function setCategorie(Categorie $categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }
}
