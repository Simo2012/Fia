<?php

namespace SceauBundle\Entity\Extranet;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SceauBundle\Entity\QuestionnaireType;
use SceauBundle\Entity\Option;

/**
 * MenuElement
 *
 * @ORM\Table(name="ExtranetMenuElement")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Extranet\MenuElementRepository")
 */
class MenuElement
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
     * @ORM\Column(name="nom", type="string", length=50, unique=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=50)
     */
    private $route;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordre", type="smallint")
     */
    private $ordre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Extranet\MenuElement", mappedBy="menuElementParent")
     */
    private $menuElementsFils;

    /**
     * @var MenuElement
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Extranet\MenuElement", inversedBy="menuElementsFils")
     * @ORM\JoinColumn(name="menuElement_id", referencedColumnName="id", nullable=true)
     */
    private $menuElementParent;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SceauBundle\Entity\QuestionnaireType")
     * @ORM\JoinTable(name="ExtranetMenuElement_QuestionnaireType")
     */
    private $questionnaireTypes;
    
    /**
     * @var Option
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Option")
     * @ORM\JoinColumn(name="option_id", referencedColumnName="id", nullable=true)
     */
    private $option;


    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->menuElementsFils = new ArrayCollection();
        $this->questionnaireTypes = new ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     *
     * @return MenuElement
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return MenuElement
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
     * Set route
     *
     * @param string $route
     *
     * @return MenuElement
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     *
     * @return MenuElement
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return MenuElement
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
     * Add menuElementsFils
     *
     * @param MenuElement $menuElementsFils
     *
     * @return MenuElement
     */
    public function addMenuElementsFil(MenuElement $menuElementsFils)
    {
        $this->menuElementsFils[] = $menuElementsFils;

        return $this;
    }

    /**
     * Remove menuElementsFils
     *
     * @param MenuElement $menuElementsFils
     */
    public function removeMenuElementsFil(MenuElement $menuElementsFils)
    {
        $this->menuElementsFils->removeElement($menuElementsFils);
    }

    /**
     * Get menuElementsFils
     *
     * @return ArrayCollection
     */
    public function getMenuElementsFils()
    {
        return $this->menuElementsFils;
    }

    /**
     * Set menuElementParent
     *
     * @param MenuElement $menuElementParent
     *
     * @return MenuElement
     */
    public function setMenuElementParent(MenuElement $menuElementParent = null)
    {
        $this->menuElementParent = $menuElementParent;

        return $this;
    }

    /**
     * Get menuElementParent
     *
     * @return MenuElement
     */
    public function getMenuElementParent()
    {
        return $this->menuElementParent;
    }

    /**
     * Add questionnaireTypes
     *
     * @param QuestionnaireType $questionnaireType
     *
     * @return MenuElement
     */
    public function addQuestionnaireType(QuestionnaireType $questionnaireType)
    {
        $this->questionnaireTypes[] = $questionnaireType;

        return $this;
    }

    /**
     * Remove questionnaireTypes
     *
     * @param QuestionnaireType $questionnaireType
     */
    public function removeQuestionnaireType(QuestionnaireType $questionnaireType)
    {
        $this->questionnaireTypes->removeElement($questionnaireType);
    }

    /**
     * Get questionnaireTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionnaireTypes()
    {
        return $this->questionnaireTypes;
    }

    /**
     * Set option
     *
     * @param Option $option
     * @return MenuElement
     */
    public function setOption(Option $option = null)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return Option
     */
    public function getOption()
    {
        return $this->option;
    }
}
