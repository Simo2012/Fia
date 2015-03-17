<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionnairePersonnalisation
 *
 * @ORM\Table(name="QuestionnairePersonnalisation")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\QuestionnairePersonnalisationRepository")
 */
class QuestionnairePersonnalisation
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetimetz")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetimetz", nullable=true)
     */
    private $dateFin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="principal", type="boolean")
     */
    private $principal;

    /**
     * @var string
     *
     * @ORM\Column(name="expediteur", type="string", length=100, nullable=true)
     */
    private $expediteur;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=255, nullable=true)
     */
    private $template;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;


    /**
     * @var QuestionnaireType
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\QuestionnaireType")
     * @ORM\JoinColumn(name="questionnaireType_id", referencedColumnName="id", nullable=false)
     */
    private $questionnaireType;

    /**
     * @var DelaiEnvoi
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\DelaiEnvoi")
     * @ORM\JoinColumn(name="delaiEnvoi_id", referencedColumnName="id", nullable=true)
     */
    private $delaiEnvoi;

    /**
     * @var DelaiReception
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\DelaiReception")
     * @ORM\JoinColumn(name="delaiReception_id", referencedColumnName="id", nullable=true)
     */
    private $delaiReception;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FIANET\SceauBundle\Entity\Version")
     * @ORM\JoinTable(name="QuestionnairePersonnalisation_Version",
     *                joinColumns={@ORM\JoinColumn(name="siteQuestionnaireType_id", referencedColumnName="id")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="version_id", referencedColumnName="id", unique=true)}
     *                )
     */
    private $versions;


    public function __construct()
    {
        $this->versions = new ArrayCollection();
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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return QuestionnairePersonnalisation
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return QuestionnairePersonnalisation
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set principal
     *
     * @param boolean $principal
     *
     * @return QuestionnairePersonnalisation
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
     * Set expediteur
     *
     * @param string $expediteur
     *
     * @return QuestionnairePersonnalisation
     */
    public function setExpediteur($expediteur)
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    /**
     * Get expediteur
     *
     * @return string
     */
    public function getExpediteur()
    {
        return $this->expediteur;
    }

    /**
     * Set template
     *
     * @param string $template
     *
     * @return QuestionnairePersonnalisation
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return QuestionnairePersonnalisation
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
     * Set description
     *
     * @param string $description
     *
     * @return QuestionnairePersonnalisation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add versions
     *
     * @param Version $version
     *
     * @return QuestionnairePersonnalisation
     */
    public function addVersion(Version $version)
    {
        $this->versions[] = $version;

        return $this;
    }

    /**
     * Remove version
     *
     * @param Version $version
     */
    public function removeVersion(Version $version)
    {
        $this->versions->removeElement($version);
    }

    /**
     * Get versions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * Set delaiEnvoi
     *
     * @param DelaiEnvoi $delaiEnvoi
     *
     * @return QuestionnairePersonnalisation
     */
    public function setDelaiEnvoi(DelaiEnvoi $delaiEnvoi = null)
    {
        $this->delaiEnvoi = $delaiEnvoi;

        return $this;
    }

    /**
     * Get delaiEnvoi
     *
     * @return DelaiEnvoi
     */
    public function getDelaiEnvoi()
    {
        return $this->delaiEnvoi;
    }

    /**
     * Set delaiReception
     *
     * @param DelaiReception $delaiReception
     *
     * @return QuestionnairePersonnalisation
     */
    public function setDelaiReception(DelaiReception $delaiReception = null)
    {
        $this->delaiReception = $delaiReception;

        return $this;
    }

    /**
     * Get delaiReception
     *
     * @return DelaiReception
     */
    public function getDelaiReception()
    {
        return $this->delaiReception;
    }

    /**
     * Set questionnaireType
     *
     * @param QuestionnaireType $questionnaireType
     *
     * @return QuestionnairePersonnalisation
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
