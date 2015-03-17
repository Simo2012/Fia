<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SousSite
 *
 * @ORM\Table(name="SousSite")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\SousSiteRepository")
 */
class SousSite
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
     * @ORM\Column(name="nom", type="string", length=100)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="idClient", type="string", length=30)
     */
    private $idClient;


    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Site", inversedBy="sousSites")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FIANET\SceauBundle\Entity\Commande", mappedBy="sousSite")
     */
    private $commandes;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FIANET\SceauBundle\Entity\Questionnaire", mappedBy="sousSite")
     */
    private $questionnaires;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->questionnaires = new ArrayCollection();
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
     * @return SousSite
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
     * Set idClient
     *
     * @param string $idClient
     *
     * @return SousSite
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;

        return $this;
    }

    /**
     * Get idClient
     *
     * @return string
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return SousSite
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
     * Add commandes
     *
     * @param Commande $commande
     * @return SousSite
     */
    public function addCommande(Commande $commande)
    {
        $this->commandes[] = $commande;

        return $this;
    }

    /**
     * Remove commandes
     *
     * @param Commande $commande
     */
    public function removeCommande(Commande $commande)
    {
        $this->commandes->removeElement($commande);
    }

    /**
     * Get commandes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * Add questionnaire
     *
     * @param Questionnaire $questionnaire
     *
     * @return SousSite
     */
    public function addQuestionnaire(Questionnaire $questionnaire)
    {
        $this->questionnaires[] = $questionnaire;

        return $this;
    }

    /**
     * Remove questionnaire
     *
     * @param Questionnaire $questionnaire
     */
    public function removeQuestionnaire(Questionnaire $questionnaire)
    {
        $this->questionnaires->removeElement($questionnaire);
    }

    /**
     * Get questionnaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionnaires()
    {
        return $this->questionnaires;
    }
}
