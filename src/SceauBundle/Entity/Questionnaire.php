<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Questionnaire
 *
 * @ORM\Table(name="Questionnaire")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\QuestionnaireRepository")
 */
class Questionnaire
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
     * @var \DateTime
     *
     * @ORM\Column(name="datePrevEnvoi", type="datetimetz")
     */
    private $datePrevEnvoi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnvoi", type="datetimetz", nullable=true)
     */
    private $dateEnvoi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateOuverture", type="datetimetz", nullable=true)
     */
    private $dateOuverture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateReponse", type="datetimetz", nullable=true)
     */
    private $dateReponse;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePrevRelance", type="datetimetz", nullable=true)
     */
    private $datePrevRelance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRelance", type="datetimetz", nullable=true)
     */
    private $dateRelance;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;


    /**
     * @var QuestionnaireType
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\QuestionnaireType")
     * @ORM\JoinColumn(name="questionnaireType_id", referencedColumnName="id", nullable=false)
     */
    private $questionnaireType;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Site", inversedBy="questionnaires")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    /**
     * @var SousSite
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\SousSite", inversedBy="questionnaires")
     * @ORM\JoinColumn(name="sousSite_id", referencedColumnName="id", nullable=true)
     */
    private $sousSite;

    /**
     * @var Commande
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Commande", cascade={"persist"})
     * @ORM\JoinColumn(name="commande_id", referencedColumnName="id", nullable=true)
     */
    private $commande;

    /**
     * @var Questionnaire
     *
     * @ORM\OneToOne(targetEntity="SceauBundle\Entity\Questionnaire")
     * @ORM\JoinColumn(name="questionnaire_id", referencedColumnName="id", nullable=true)
     *
     */
    private $questionnaireLie;

    /**
     * @var Langue
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Langue")
     * @ORM\JoinColumn(name="langue_id", referencedColumnName="id", nullable=false)
     */
    private $langue;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\QuestionnaireReponse", mappedBy="questionnaire")
     */
    private $questionnaireReponses;

    /**
     * @var Membre
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Membre", inversedBy="questionnaires")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id", nullable=true)
     */
    private $membre;


    public function __construct()
    {
        $this->questionnaireReponses = new ArrayCollection();
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
     * @return Questionnaire
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
     * @return Questionnaire
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
     * @param \DateTime $datePrevEnvoi
     *
     * @return Questionnaire
     */
    public function setDatePrevEnvoi($datePrevEnvoi)
    {
        $this->datePrevEnvoi = $datePrevEnvoi;

        return $this;
    }

    /**
     * Get datePrevEnvoi
     *
     * @return \DateTime
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
     * @return Questionnaire
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
     * @return Questionnaire
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
     * @return Questionnaire
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
     * Set datePrevRelance
     *
     * @param \DateTime $datePrevRelance
     * @return Questionnaire
     */
    public function setDatePrevRelance($datePrevRelance)
    {
        $this->datePrevRelance = $datePrevRelance;

        return $this;
    }

    /**
     * Get datePrevRelance
     *
     * @return \DateTime
     */
    public function getDatePrevRelance()
    {
        return $this->datePrevRelance;
    }

    /**
     * Set dateRelance
     *
     * @param \DateTime $dateRelance
     *
     * @return Questionnaire
     */
    public function setDateRelance($dateRelance)
    {
        $this->dateRelance = $dateRelance;

        return $this;
    }

    /**
     * Get dateRelance
     *
     * @return \DateTime
     */
    public function getDateRelance()
    {
        return $this->dateRelance;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Questionnaire
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
     * Set commande
     *
     * @param Commande $commande
     *
     * @return Questionnaire
     */
    public function setCommande(Commande $commande = null)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set questionnaireLie
     *
     * @param Questionnaire $questionnaireLie
     *
     * @return Questionnaire
     */
    public function setQuestionnaireLie(Questionnaire $questionnaireLie = null)
    {
        $this->questionnaireLie = $questionnaireLie;

        return $this;
    }

    /**
     * Get questionnaireLie
     *
     * @return Questionnaire
     */
    public function getQuestionnaireLie()
    {
        return $this->questionnaireLie;
    }

    /**
     * Set langue
     *
     * @param Langue $langue
     *
     * @return Questionnaire
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
     * Set questionnaireType
     *
     * @param QuestionnaireType $questionnaireType
     *
     * @return Questionnaire
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

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return Questionnaire
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
     * Add questionnaireReponse
     *
     * @param QuestionnaireReponse $questionnaireReponse
     *
     * @return Questionnaire
     */
    public function addQuestionnaireReponse(QuestionnaireReponse $questionnaireReponse)
    {
        $this->questionnaireReponses[] = $questionnaireReponse;

        return $this;
    }

    /**
     * Remove questionnaireReponse
     *
     * @param QuestionnaireReponse $questionnaireReponse
     */
    public function removeQuestionnaireReponse(QuestionnaireReponse $questionnaireReponse)
    {
        $this->questionnaireReponses->removeElement($questionnaireReponse);
    }

    /**
     * Get questionnaireReponses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionnaireReponses()
    {
        return $this->questionnaireReponses;
    }

    /**
     * Set membre
     *
     * @param Membre $membre
     *
     * @return Questionnaire
     */
    public function setMembre(Membre $membre = null)
    {
        $this->membre = $membre;

        return $this;
    }

    /**
     * Get membre
     *
     * @return Membre
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * Set sousSite
     *
     * @param SousSite $sousSite
     *
     * @return Questionnaire
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
}
