<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Categorie
 *
 * @ORM\Table(name="Categorie")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\CategorieRepository")
 */
class Categorie
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
     * @ORM\Column(name="libelleCourt", type="string", length=50)
     */
    private $libelleCourt;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=100)
     */
    private $slug;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accueil", type="boolean")
     */
    private $accueil;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\SiteCategorie", mappedBy="categorie")
     */
    private $siteCategories;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Categorie", mappedBy="categoriePrimaire")
     */
    private $categoriesSecondaires;

    /**
     * @var Categorie
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Categorie", inversedBy="categoriesSecondaires")
     * @ORM\JoinColumn(name="categorie_id", referencedColumnName="id", nullable=true)
     */
    private $categoriePrimaire;


    public function __construct()
    {
        $this->siteCategories = new ArrayCollection();
        $this->categoriesSecondaires = new ArrayCollection();
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
     * @return Categorie
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
     * Set libelleCourt
     *
     * @param string $libelleCourt
     *
     * @return Categorie
     */
    public function setLibelleCourt($libelleCourt)
    {
        $this->libelleCourt = $libelleCourt;

        return $this;
    }

    /**
     * Get libelleCourt
     *
     * @return string
     */
    public function getLibelleCourt()
    {
        return $this->libelleCourt;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Categorie
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Categorie
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
     * Set accueil
     *
     * @param boolean $accueil
     *
     * @return Categorie
     */
    public function setAccueil($accueil)
    {
        $this->accueil = $accueil;

        return $this;
    }

    /**
     * Get accueil
     *
     * @return boolean
     */
    public function getAccueil()
    {
        return $this->accueil;
    }

    /**
     * Add siteCategorie
     *
     * @param SiteCategorie $siteCategorie
     *
     * @return Categorie
     */
    public function addSiteCategorie(SiteCategorie $siteCategorie)
    {
        $this->siteCategories[] = $siteCategorie;

        return $this;
    }

    /**
     * Remove siteCategorie
     *
     * @param SiteCategorie $siteCategorie
     */
    public function removeSiteCategorie(SiteCategorie $siteCategorie)
    {
        $this->siteCategories->removeElement($siteCategorie);
    }

    /**
     * Get siteCategories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSiteCategories()
    {
        return $this->siteCategories;
    }

    /**
     * Add categoriesSecondaire
     *
     * @param Categorie $categorieSecondaire
     *
     * @return Categorie
     */
    public function addCategorieSecondaire(Categorie $categorieSecondaire)
    {
        $this->categoriesSecondaires[] = $categorieSecondaire;

        return $this;
    }

    /**
     * Remove categoriesSecondaire
     *
     * @param Categorie $categorieSecondaire
     */
    public function removeCategorieSecondaire(Categorie $categorieSecondaire)
    {
        $this->categoriesSecondaires->removeElement($categorieSecondaire);
    }

    /**
     * Get categoriesSecondaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoriesSecondaires()
    {
        return $this->categoriesSecondaires;
    }

    /**
     * Set categoriePrimaire
     *
     * @param Categorie $categoriePrimaire
     *
     * @return Categorie
     */
    public function setCategoriePrimaire(Categorie $categoriePrimaire = null)
    {
        $this->categoriePrimaire = $categoriePrimaire;

        return $this;
    }

    /**
     * Get categoriePrimaire
     *
     * @return Categorie
     */
    public function getCategoriePrimaire()
    {
        return $this->categoriePrimaire;
    }
}
