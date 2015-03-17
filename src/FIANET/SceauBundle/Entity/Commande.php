<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="Commande")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\CommandeRepository")
 */
class Commande
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
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=50)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50)
     */
    private $nom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetimetz")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=50, nullable=true)
     */
    private $reference;

    /**
     * @var integer
     *
     * @ORM\Column(name="montant", type="integer", nullable=true)
     */
    private $montant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUtilisation", type="datetimetz", nullable=true)
     */
    private $dateUtilisation;

    /**
     * @var array
     *
     * @ORM\Column(name="donnees", type="json_array", nullable=true)
     */
    private $donnees;


    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Site", inversedBy="commandes")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    /**
     * @var SousSite
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\SousSite", inversedBy="commandes")
     * @ORM\JoinColumn(name="sousSite_id", referencedColumnName="id", nullable=true)
     */
    private $sousSite;

    /**
     * @var Flux
     *
     * @ORM\OneToOne(targetEntity="FIANET\SceauBundle\Entity\Flux")
     * @ORM\JoinColumn(name="flux_id", referencedColumnName="id", nullable=true)
     */
    private $flux;

    /**
     * @var QuestionnaireType
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\QuestionnaireType")
     * @ORM\JoinColumn(name="questionnaireType_id", referencedColumnName="id", nullable=false)
     */
    private $questionnaireType;

    /**
     * @var Langue
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Langue")
     * @ORM\JoinColumn(name="langue_id", referencedColumnName="id", nullable=false)
     */
    private $langue;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FIANET\SceauBundle\Entity\LivraisonType")
     * @ORM\JoinTable(name="Commande_LivraisonType",
     *      joinColumns={@ORM\JoinColumn(name="commande_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="livraisonType_id", referencedColumnName="id")}
     *      )
     */
    private $livraisonTypes;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FIANET\SceauBundle\Entity\Produit")
     * @ORM\JoinTable(name="Commande_Produit",
     *      joinColumns={@ORM\JoinColumn(name="commande_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="produit_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $produits;


    public function __construct()
    {
        $this->livraisonTypes = new ArrayCollection();
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
     * @return Commande
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
     * Set prenom
     *
     * @param string $prenom
     * @return Commande
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Commande
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
     * Set date
     *
     * @param \DateTime $date
     * @return Commande
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Commande
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set montant
     *
     * @param integer $montant
     * @return Commande
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return integer
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set dateUtilisation
     *
     * @param \DateTime $dateUtilisation
     * @return Commande
     */
    public function setDateUtilisation($dateUtilisation)
    {
        $this->dateUtilisation = $dateUtilisation;

        return $this;
    }

    /**
     * Get dateUtilisation
     *
     * @return \DateTime
     */
    public function getDateUtilisation()
    {
        return $this->dateUtilisation;
    }

    /**
     * Set donnees
     *
     * @param array $donnees
     * @return Commande
     */
    public function setDonnees($donnees)
    {
        $this->donnees = $donnees;

        return $this;
    }

    /**
     * Get donnees
     *
     * @return array
     */
    public function getDonnees()
    {
        return $this->donnees;
    }

    /**
     * Set sousSite
     *
     * @param SousSite $sousSite
     *
     * @return Commande
     */
    public function setSousSite(SousSite $sousSite = null)
    {
        $this->sousSite = $sousSite;

        return $this;
    }

    /**
     * Get sousSite
     *
     * @return SousSite
     */
    public function getSousSite()
    {
        return $this->sousSite;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return Commande
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
     * Set flux
     *
     * @param Flux $flux
     *
     * @return Commande
     */
    public function setFlux(Flux $flux = null)
    {
        $this->flux = $flux;

        return $this;
    }

    /**
     * Get flux
     *
     * @return Flux
     */
    public function getFlux()
    {
        return $this->flux;
    }

    /**
     * Set langue
     *
     * @param Langue $langue
     *
     * @return Commande
     */
    public function setLangue(Langue $langue)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Add livraisonType
     *
     * @param LivraisonType $livraisonType
     *
     * @return Commande
     */
    public function addLivraisonType(LivraisonType $livraisonType)
    {
        $this->livraisonTypes[] = $livraisonType;

        return $this;
    }

    /**
     * Remove livraisonType
     *
     * @param LivraisonType $livraisonType
     */
    public function removeLivraisonType(LivraisonType $livraisonType)
    {
        $this->livraisonTypes->removeElement($livraisonType);
    }

    /**
     * Get livraisonTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLivraisonTypes()
    {
        return $this->livraisonTypes;
    }

    /**
     * Add produit
     *
     * @param Produit $produit
     *
     * @return Commande
     */
    public function addProduit(Produit $produit)
    {
        $this->produits[] = $produit;

        return $this;
    }

    /**
     * Remove produit
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
     * Set questionnaireType
     *
     * @param QuestionnaireType $questionnaireType
     *
     * @return Commande
     */
    public function setQuestionnaireType(QuestionnaireType $questionnaireType)
    {
        $this->questionnaireType = $questionnaireType;

        return $this;
    }

    /**
     * Get questionnaireType
     *
     * @return QuestionnaireType
     */
    public function getQuestionnaireType()
    {
        return $this->questionnaireType;
    }
}
