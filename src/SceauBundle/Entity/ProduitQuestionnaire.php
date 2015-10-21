<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProduitQuestionnaire
 *
 * @ORM\Table(name="ProduitQuestionnaire")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\ProduitQuestionnaireRepository")
 */
class ProduitQuestionnaire
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
     * @ORM\Column(name="email", type="string", length=100)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateInsertion", type="datetimetz")
     */
    private $dateInsertion;

    /**
     * @var string
     *
     * @ORM\Column(name="datePrevEnvoi", type="datetimetz")
     */
    private $datePrevEnvoi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnvoi", type="datetimetz")
     */
    private $dateEnvoi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateOuverture", type="datetimetz")
     */
    private $dateOuverture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateReponse", type="datetimetz")
     */
    private $dateReponse;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SceauBundle\Entity\Produit")
     * @ORM\JoinTable(name="ProduitQuestionnaire_Produit",
     *      joinColumns={@ORM\JoinColumn(name="produitQuestionnaire_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="produit_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $produits;

    /**
     * @var QuestionnaireReponse
     *
     * @ORM\OneToOne(targetEntity="SceauBundle\Entity\ProduitQuestionnaireReponse")
     * @ORM\JoinColumn(name="produitQuestionnaireReponse_id", referencedColumnName="id", nullable=true)
     */
    private $produitQuestionnaireReponse;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Site", inversedBy="produitQuestionnaires")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;


    public function __construct()
    {
        $this->produits = new ArrayCollection();
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
     * Set email
     *
     * @param string $email
     *
     * @return ProduitQuestionnaire
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateInsertion
     *
     * @param \DateTime $dateInsertion
     *
     * @return ProduitQuestionnaire
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
     * Set datePrevEnvoi
     *
     * @param string $datePrevEnvoi
     *
     * @return ProduitQuestionnaire
     */
    public function setDatePrevEnvoi($datePrevEnvoi)
    {
        $this->datePrevEnvoi = $datePrevEnvoi;

        return $this;
    }

    /**
     * Get datePrevEnvoi
     *
     * @return string
     */
    public function getDatePrevEnvoi()
    {
        return $this->datePrevEnvoi;
    }

    /**
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     *
     * @return ProduitQuestionnaire
     */
    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    /**
     * Get dateEnvoi
     *
     * @return \DateTime
     */
    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
    }

    /**
     * Set dateOuverture
     *
     * @param \DateTime $dateOuverture
     *
     * @return ProduitQuestionnaire
     */
    public function setDateOuverture($dateOuverture)
    {
        $this->dateOuverture = $dateOuverture;

        return $this;
    }

    /**
     * Get dateOuverture
     *
     * @return \DateTime
     */
    public function getDateOuverture()
    {
        return $this->dateOuverture;
    }

    /**
     * Set dateReponse
     *
     * @param \DateTime $dateReponse
     *
     * @return ProduitQuestionnaire
     */
    public function setDateReponse($dateReponse)
    {
        $this->dateReponse = $dateReponse;

        return $this;
    }

    /**
     * Get dateReponse
     *
     * @return \DateTime
     */
    public function getDateReponse()
    {
        return $this->dateReponse;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return ProduitQuestionnaire
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
     * Add produit
     *
     * @param Produit $produit
     *
     * @return ProduitQuestionnaire
     */
    public function addProduit(Produit $produit)
    {
        $this->produits[] = $produit;

        return $this;
    }

    /**
     * Remove produits
     *
     * @param Produit $produit
     */
    public function removeProduit(Produit $produit)
    {
        $this->produits->removeElement($produit);
    }

    /**
     * Get produits
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProduits()
    {
        return $this->produits;
    }
    
    /**
     * Set site
     *
     * @param Site $site
     *
     * @return ProduitQuestionnaire
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
     * Set produitQuestionnaireReponse
     *
     * @param ProduitQuestionnaireReponse $produitQuestionnaireReponse
     *
     * @return ProduitQuestionnaire
     */
    public function setProduitQuestionnaireReponse(ProduitQuestionnaireReponse $produitQuestionnaireReponse = null)
    {
        $this->produitQuestionnaireReponse = $produitQuestionnaireReponse;

        return $this;
    }

    /**
     * Get produitQuestionnaireReponse
     *
     * @return ProduitQuestionnaireReponse
     */
    public function getProduitQuestionnaireReponse()
    {
        return $this->produitQuestionnaireReponse;
    }
}
