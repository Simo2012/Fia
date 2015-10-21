<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Reponse
 *
 * @ORM\Table(name="Reponse")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\ReponseRepository")
 * @Gedmo\TranslationEntity(class="SceauBundle\Entity\Traduction\ReponseTraduction")
 */
class Reponse implements Translatable
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
     * @ORM\Column(name="libelle", type="string", length=255)
     * @Gedmo\Translatable
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="libelleCourt", type="string", length=255)
     */
    private $libelleCourt;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordre", type="smallint")
     */
    private $ordre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="precision", type="boolean")
     */
    private $precision;


    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Question", inversedBy="reponses")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false)
     */
    private $question;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\QuestionnaireReponse", mappedBy="reponse")
     */
    private $questionnaireReponses;

    /**
     * @var ReponseStatut
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\ReponseStatut")
     * @ORM\JoinColumn(name="reponseStatut_id", referencedColumnName="id", nullable=false)
     */
    private $reponseStatut;


    /**
     * Post locale
     * Used locale to override Translation listener's locale
     *
     * @Gedmo\Locale
     */
    protected $locale;


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
     * Set libelle
     *
     * @param string $libelle
     * @return Reponse
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
     * @return Reponse
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
     *
     * @return Reponse
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
     * Set precision
     *
     * @param boolean $precision
     *
     * @return Reponse
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * Get precision
     *
     * @return boolean
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * Set question
     *
     * @param Question $question
     *
     * @return Reponse
     */
    public function setQuestion(Question $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Add questionnaireReponse
     *
     * @param QuestionnaireReponse $questionnaireReponse
     *
     * @return Reponse
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
     * Set reponseStatut
     *
     * @param ReponseStatut $reponseStatut
     *
     * @return Reponse
     */
    public function setReponseStatut(ReponseStatut $reponseStatut)
    {
        $this->reponseStatut = $reponseStatut;

        return $this;
    }

    /**
     * Get reponseStatut
     *
     * @return ReponseStatut
     */
    public function getReponseStatut()
    {
        return $this->reponseStatut;
    }


    /**
     * Sets translatable locale
     *
     * @param string $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
