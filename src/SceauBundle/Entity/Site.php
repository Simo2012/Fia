<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Site
 *
 * @ORM\Table(name="Site")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\SiteRepository")
 */
class Site
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
     * @ORM\Column(name="url", type="string", length=150)
     */
    private $url;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"nom"})
     * @ORM\Column(name="slug", type="string", length=150, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="clePrivee", type="string", length=20, nullable=true)
     */
    private $clePrivee;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SceauBundle\Entity\QuestionnairePersonnalisation")
     * @ORM\JoinTable(name="Site_QuestionnairePersonnalisation",
     *      joinColumns={@ORM\JoinColumn(name="site_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(
     *          name="questionnairePersonnalisation_id", referencedColumnName="id", unique=true
     *      )})
     */
    private $questionnairePersonnalisations;

    /**
     * @var Package
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Package")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="id", nullable=false)
     **/
    private $package;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\SiteOption", mappedBy="site")
     **/
    private $siteOptions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Site", mappedBy="sitePrincipal")
     */
    private $siteMiroirs;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Site", inversedBy="siteMiroirs")
     * @ORM\JoinColumn(name="sitePrincipal_id", referencedColumnName="id", nullable=true)
     */
    private $sitePrincipal;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\SousSite", mappedBy="site")
     */
    private $sousSites;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Flux", mappedBy="site")
     */
    private $flux;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Commande", mappedBy="site")
     */
    private $commandes;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Questionnaire", mappedBy="site")
     */
    private $questionnaires;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Question", mappedBy="site")
     */
    private $questions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\SiteCategorie", mappedBy="site")
     */
    private $siteCategories;

    /**
     * @var Societe
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Societe", inversedBy="sites")
     * @ORM\JoinColumn(name="societe_id", referencedColumnName="id", nullable=false)
     */
    private $societe;

    /**
     * @var SiteType
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\SiteType")
     * @ORM\JoinColumn(name="siteType_id", referencedColumnName="id", nullable=false)
     */
    private $siteType;

    /**
     * @var AdministrationType
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\AdministrationType")
     * @ORM\JoinColumn(name="administrationType_id", referencedColumnName="id", nullable=false)
     */
    private $administrationType;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Relance", mappedBy="site")
     */
    private $relances;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\ProduitQuestionnaire", mappedBy="site")
     */
    private $produitQuestionnaires;


    public function __construct()
    {
        $this->questionnaires = new ArrayCollection();
        $this->siteOptions = new ArrayCollection();
        $this->siteFils = new ArrayCollection();
        $this->sousSites = new ArrayCollection();
        $this->flux = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->siteCategories = new ArrayCollection();
        $this->relances = new ArrayCollection();
        $this->produitQuestionnaires = new ArrayCollection();
        $this->questionnairePersonnalisations = new ArrayCollection();
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
     * @return Site
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
     * Set package
     *
     * @param Package $package
     *
     * @return Site
     */
    public function setPackage(Package $package)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Get package
     *
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Add siteOptions
     *
     * @param SiteOption $siteOptions
     *
     * @return Site
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

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Site
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
     * Set url
     *
     * @param string $url
     * @return Site
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Add sousSites
     *
     * @param SousSite $sousSite
     *
     * @return Site
     */
    public function addSousSite(SousSite $sousSite)
    {
        $this->sousSites[] = $sousSite;

        return $this;
    }

    /**
     * Remove sousSites
     *
     * @param SousSite $sousSite
     */
    public function removeSousSite(SousSite $sousSite)
    {
        $this->sousSites->removeElement($sousSite);
    }

    /**
     * Get sousSites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSousSites()
    {
        return $this->sousSites;
    }

    /**
     * Add siteMiroir
     *
     * @param Site $siteMiroirs
     * @return Site
     */
    public function addSiteMiroir(Site $siteMiroirs)
    {
        $this->siteMiroirs[] = $siteMiroirs;

        return $this;
    }

    /**
     * Remove siteMiroir
     *
     * @param Site $siteMiroir
     */
    public function removeSiteMiroir(Site $siteMiroir)
    {
        $this->siteMiroirs->removeElement($siteMiroir);
    }

    /**
     * Get siteMiroirs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSiteMiroirs()
    {
        return $this->siteMiroirs;
    }

    /**
     * Set sitePrincipal
     *
     * @param Site $sitePrincipal
     *
     * @return Site
     */
    public function setSitePrincipal(Site $sitePrincipal = null)
    {
        $this->sitePrincipal = $sitePrincipal;

        return $this;
    }

    /**
     * Get sitePrincipal
     *
     * @return Site
     */
    public function getSitePrincipal()
    {
        return $this->sitePrincipal;
    }

    /**
     * Add commande
     *
     * @param Commande $commande
     *
     * @return Site
     */
    public function addCommande(Commande $commande)
    {
        $this->commandes[] = $commande;

        return $this;
    }

    /**
     * Remove commande
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
     * Add flux
     *
     * @param Flux $flux
     *
     * @return Site
     */
    public function addFlux(Flux $flux)
    {
        $this->flux[] = $flux;

        return $this;
    }

    /**
     * Remove flux
     *
     * @param Flux $flux
     */
    public function removeFlux(Flux $flux)
    {
        $this->flux->removeElement($flux);
    }

    /**
     * Get flux
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlux()
    {
        return $this->flux;
    }

    /**
     * Add question
     *
     * @param Question $question
     *
     * @return Site
     */
    public function addQuestion(Question $question)
    {
        $this->questions[] = $question;

        return $this;
    }

    /**
     * Remove question
     *
     * @param Question $question
     */
    public function removeQuestion(Question $question)
    {
        $this->questions->removeElement($question);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Add siteCategories
     *
     * @param SiteCategorie $siteCategorie
     *
     * @return Site
     */
    public function addSiteCategorie(SiteCategorie $siteCategorie)
    {
        $this->siteCategories[] = $siteCategorie;

        return $this;
    }

    /**
     * Remove siteCategories
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
     * Set societe
     *
     * @param Societe $societe
     *
     * @return Site
     */
    public function setSociete($societe)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return Societe
     */
    public function getSociete()
    {
        return $this->societe;
    }

    /**
     * Set siteType
     *
     * @param SiteType $siteType
     *
     * @return Site
     */
    public function setSiteType(SiteType $siteType)
    {
        $this->siteType = $siteType;

        return $this;
    }

    /**
     * Get siteType
     *
     * @return SiteType
     */
    public function getSiteType()
    {
        return $this->siteType;
    }

    /**
     * Set administrationType
     *
     * @param AdministrationType $administrationType
     *
     * @return Site
     */
    public function setAdministrationType(AdministrationType $administrationType)
    {
        $this->administrationType = $administrationType;

        return $this;
    }

    /**
     * Get administrationType
     *
     * @return AdministrationType
     */
    public function getAdministrationType()
    {
        return $this->administrationType;
    }

    /**
     * Add relance
     *
     * @param Relance $relance
     *
     * @return Site
     */
    public function addRelance(Relance $relance)
    {
        $this->relances[] = $relance;

        return $this;
    }

    /**
     * Remove relance
     *
     * @param Relance $relance
     */
    public function removeRelance(Relance $relance)
    {
        $this->relances->removeElement($relance);
    }

    /**
     * Get relances
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRelances()
    {
        return $this->relances;
    }

    /**
     * Add produitQuestionnaire
     *
     * @param ProduitQuestionnaire $produitQuestionnaire
     *
     * @return Site
     */
    public function addProduitQuestionnaire(ProduitQuestionnaire $produitQuestionnaire)
    {
        $this->produitQuestionnaires[] = $produitQuestionnaire;

        return $this;
    }

    /**
     * Remove produitQuestionnaire
     *
     * @param ProduitQuestionnaire $produitQuestionnaire
     */
    public function removeProduitQuestionnaire(ProduitQuestionnaire $produitQuestionnaire)
    {
        $this->produitQuestionnaires->removeElement($produitQuestionnaire);
    }

    /**
     * Get produitQuestionnaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProduitQuestionnaires()
    {
        return $this->produitQuestionnaires;
    }

    /**
     * Add questionnairePersonnalisation
     *
     * @param QuestionnairePersonnalisation $questionnairePersonnalisation
     * @return Site
     */
    public function addQuestionnairePersonnalisation(QuestionnairePersonnalisation $questionnairePersonnalisation)
    {
        $this->questionnairePersonnalisations[] = $questionnairePersonnalisation;

        return $this;
    }

    /**
     * Remove questionnairePersonnalisation
     *
     * @param QuestionnairePersonnalisation $questionnairePersonnalisation
     */
    public function removeQuestionnairePersonnalisation(QuestionnairePersonnalisation $questionnairePersonnalisation)
    {
        $this->questionnairePersonnalisations->removeElement($questionnairePersonnalisation);
    }

    /**
     * Get questionnairePersonnalisations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionnairePersonnalisations()
    {
        return $this->questionnairePersonnalisations;
    }

    /**
     * Add questionnaire
     *
     * @param Questionnaire $questionnaire
     *
     * @return Site
     */
    public function addQuestionnaire(Questionnaire $questionnaire)
    {
        $this->questionnaires[] = $questionnaire;

        return $this;
    }

    /**
     * Remove questionnaires
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

    /**
     * Set clePrivee
     *
     * @param string $clePrivee
     *
     * @return Site
     */
    public function setClePrivee($clePrivee)
    {
        $this->clePrivee = $clePrivee;

        return $this;
    }

    /**
     * Get clePrivee
     *
     * @return string
     */
    public function getClePrivee()
    {
        return $this->clePrivee;
    }


    /**
     * Permet de récupérer la clé privée "Sceau". Elle sert par exemple à authentifier un site qui envoie des flux XML.
     *
     * @return string
     */
    public function getClePriveeSceau()
    {
        return md5($this->id . '_sceau_' . $this->clePrivee);
    }
}
