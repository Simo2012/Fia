<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="Question")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\QuestionRepository")
 */
class Question
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
     * @ORM\Column(name="libelle", type="string", length=200)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="libelleCourt", type="string", length=200)
     */
    private $libelleCourt;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordre", type="smallint")
     */
    private $ordre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetimetz", nullable=true)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetimetz", nullable=true)
     */
    private $dateFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="page", type="smallint")
     */
    private $page;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cache", type="boolean")
     */
    private $cache;

    /**
     * @var array
     *
     * @ORM\Column(name="visible", type="json_array", nullable=true)
     */
    private $visible;

    /**
     * @var array
     *
     * @ORM\Column(name="obligatoire", type="json_array", nullable=true)
     */
    private $obligatoire;

    /**
     * @var string
     *
     * @ORM\Column(name="valeurMin", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $valeurMin;

    /**
     * @var string
     *
     * @ORM\Column(name="valeurMax", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $valeurMax;


    /**
     * @var QuestionnaireType
     *
     * @ORM\ManyToMany(targetEntity="FIANET\SceauBundle\Entity\QuestionnaireType", inversedBy="questions")
     * @ORM\JoinTable(name="QuestionnaireType_Question")
     */
    private $questionnaireTypes;

    /**
     * @var QuestionType
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\QuestionType")
     * @ORM\JoinColumn(name="questionType_id", referencedColumnName="id", nullable=false)
     */
    private $questionType;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FIANET\SceauBundle\Entity\Question", mappedBy="questionPrimaire")
     */
    private $questionsSecondaires;

    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Question", inversedBy="questionsSecondaires")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=true)
     */
    private $questionPrimaire;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FIANET\SceauBundle\Entity\Reponse", mappedBy="question", cascade={"persist"})
     */
    private $reponses;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FIANET\SceauBundle\Entity\LivraisonType")
     * @ORM\JoinTable(name="Question_LivraisonType",
     *      joinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="livraisonType_id", referencedColumnName="id")}
     *      )
     **/
    private $livraisonTypes;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FIANET\SceauBundle\Entity\NotationLibelle")
     * @ORM\JoinTable(name="Question_NotationLibelle",
     *      joinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="notationLibelle_id", referencedColumnName="id")}
     *      )
     */
    private $notationLibelles;

    /**
     * @var QuestionStatut
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\QuestionStatut")
     * @ORM\JoinColumn(name="questionStatut_id", referencedColumnName="id", nullable=false)
     */
    private $questionStatut;

    /**
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Site", inversedBy="questions")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questionsSecondaires = new ArrayCollection();
        $this->reponses = new ArrayCollection();
        $this->livraisonTypes = new ArrayCollection();
        $this->notationLibelles = new ArrayCollection();
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
     * Set libelle
     *
     * @param string $libelle
     * @return Question
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
     * @return Question
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
     * Set ordre
     *
     * @param integer $ordre
     * @return Question
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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return Question
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
     * @return Question
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
     * Set questionType
     *
     * @param QuestionType $questionType
     *
     * @return Question
     */
    public function setQuestionType(QuestionType $questionType)
    {
        $this->questionType = $questionType;

        return $this;
    }

    /**
     * Get questionType
     *
     * @return QuestionType
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }

    /**
     * Add questionsSecondaires
     *
     * @param Question $questionsSecondaires
     *
     * @return Question
     */
    public function addQuestionsSecondaire(Question $questionsSecondaires)
    {
        $this->questionsSecondaires[] = $questionsSecondaires;

        return $this;
    }

    /**
     * Remove questionsSecondaires
     *
     * @param Question $questionsSecondaires
     */
    public function removeQuestionsSecondaire(Question $questionsSecondaires)
    {
        $this->questionsSecondaires->removeElement($questionsSecondaires);
    }

    /**
     * Get questionsSecondaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionsSecondaires()
    {
        return $this->questionsSecondaires;
    }

    /**
     * Set questionPrimaire
     *
     * @param Question $questionPrimaire
     * @return Question
     */
    public function setQuestionPrimaire(Question $questionPrimaire = null)
    {
        $this->questionPrimaire = $questionPrimaire;

        return $this;
    }

    /**
     * Get questionPrimaire
     *
     * @return Question
     */
    public function getQuestionPrimaire()
    {
        return $this->questionPrimaire;
    }

    /**
     * Add reponse
     *
     * @param Reponse $reponse
     *
     * @return Question
     */
    public function addReponse(Reponse $reponse)
    {
        $this->reponses[] = $reponse;
        $reponse->setQuestion($this);

        return $this;
    }

    /**
     * Remove reponse
     *
     * @param Reponse $reponse
     */
    public function removeReponse(Reponse $reponse)
    {
        $this->reponses->removeElement($reponse);
    }

    /**
     * Get reponses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReponses()
    {
        return $this->reponses;
    }

    /**
     * Add livraisonType
     *
     * @param LivraisonType $livraisonType
     *
     * @return Question
     */
    public function addLivraisonType(LivraisonType $livraisonType)
    {
        $this->livraisonTypes[] = $livraisonType;

        return $this;
    }

    /**
     * Remove livraisonTypes
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
     * Add notationLibelles
     *
     * @param NotationLibelle $notationLibelles
     *
     * @return Question
     */
    public function addNotationLibelle(NotationLibelle $notationLibelles)
    {
        $this->notationLibelles[] = $notationLibelles;

        return $this;
    }

    /**
     * Remove notationLibelles
     *
     * @param NotationLibelle $notationLibelles
     */
    public function removeNotationLibelle(NotationLibelle $notationLibelles)
    {
        $this->notationLibelles->removeElement($notationLibelles);
    }

    /**
     * Get notationLibelles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotationLibelles()
    {
        return $this->notationLibelles;
    }

    /**
     * Set page
     *
     * @param integer $page
     * @return Question
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set cache
     *
     * @param boolean $cache
     *
     * @return Question
     */
    public function setCache($cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get cache
     *
     * @return boolean
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set visible
     *
     * @param array $visible
     *
     * @return Question
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return array
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set obligatoire
     *
     * @param array $obligatoire
     *
     * @return Question
     */
    public function setObligatoire($obligatoire)
    {
        $this->obligatoire = $obligatoire;

        return $this;
    }

    /**
     * Get obligatoire
     *
     * @return array
     */
    public function getObligatoire()
    {
        return $this->obligatoire;
    }

    /**
     * Set questionStatut
     *
     * @param QuestionStatut $questionStatut
     *
     * @return Question
     */
    public function setQuestionStatut(QuestionStatut $questionStatut = null)
    {
        $this->questionStatut = $questionStatut;

        return $this;
    }

    /**
     * Get questionStatut
     *
     * @return QuestionStatut
     */
    public function getQuestionStatut()
    {
        return $this->questionStatut;
    }

    /**
     * Set valeurMin
     *
     * @param string $valeurMin
     *
     * @return Reponse
     */
    public function setValeurMin($valeurMin)
    {
        $this->valeurMin = $valeurMin;

        return $this;
    }

    /**
     * Get valeurMin
     *
     * @return string
     */
    public function getValeurMin()
    {
        return $this->valeurMin;
    }

    /**
     * Set valeurMax
     *
     * @param string $valeurMax
     *
     * @return Reponse
     */
    public function setValeurMax($valeurMax)
    {
        $this->valeurMax = $valeurMax;

        return $this;
    }

    /**
     * Get valeurMax
     *
     * @return string
     */
    public function getValeurMax()
    {
        return $this->valeurMax;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return Question
     */
    public function setSite(Site $site = null)
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
     * Add questionnaireType
     *
     * @param QuestionnaireType $questionnaireType
     *
     * @return Question
     */
    public function addQuestionnaireType(QuestionnaireType $questionnaireType)
    {
        $this->questionnaireTypes[] = $questionnaireType;

        return $this;
    }

    /**
     * Remove questionnaireType
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
}
