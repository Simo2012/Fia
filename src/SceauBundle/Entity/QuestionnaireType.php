<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * QuestionnaireType
 *
 * @ORM\Table(name="QuestionnaireType")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\QuestionnaireTypeRepository")
 * @Gedmo\TranslationEntity(class="SceauBundle\Entity\Traduction\QuestionnaireTypeTraduction")
 */
class QuestionnaireType implements Translatable
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
     * @Gedmo\Translatable
     */
    private $libelle;

    /**
     * @var array
     *
     * @ORM\Column(name="parametrage", type="jsonb_array")
     */
    private $parametrage;


    /**
     * @var QuestionnaireType
     *
     * @ORM\OneToOne(targetEntity="SceauBundle\Entity\QuestionnaireType")
     * @ORM\JoinColumn(name="questionnaireType_id", referencedColumnName="id", nullable=true)
     */
    private $questionnaireTypeSuivant;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SceauBundle\Entity\Question", mappedBy="questionnaireTypes")
     */
    private $questions;

    /**
     * @var DelaiEnvoi
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\DelaiEnvoi")
     * @ORM\JoinColumn(name="delaiEnvoi_id", referencedColumnName="id", nullable=false)
     */
    private $delaiEnvoi;

    /**
     * @var DelaiReception
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\DelaiReception")
     * @ORM\JoinColumn(name="delaiReception_id", referencedColumnName="id", nullable=false)
     */
    private $delaiReception;


    /**
     * Post locale
     * Used locale to override Translation listener's locale
     *
     * @Gedmo\Locale
     */
    protected $locale;


    public function __construct()
    {
        $this->questions = new ArrayCollection();
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
     * @return QuestionnaireType
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
     * Set parametrage
     *
     * @param array $parametrage
     * @return QuestionnaireType
     */
    public function setParametrage($parametrage)
    {
        $this->parametrage = $parametrage;

        return $this;
    }

    /**
     * Get parametrage
     *
     * @return array
     */
    public function getParametrage()
    {
        return $this->parametrage;
    }

    /**
     * Set questionnaireTypeSuivant
     *
     * @param QuestionnaireType $questionnaireTypeSuivant
     *
     * @return QuestionnaireType
     */
    public function setQuestionnaireTypeSuivant(QuestionnaireType $questionnaireTypeSuivant = null)
    {
        $this->questionnaireTypeSuivant = $questionnaireTypeSuivant;

        return $this;
    }

    /**
     * Get questionnaireTypeSuivant
     *
     * @return QuestionnaireType
     */
    public function getQuestionnaireTypeSuivant()
    {
        return $this->questionnaireTypeSuivant;
    }

    /**
     * Add question
     *
     * @param Question $question
     *
     * @return QuestionnaireType
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
     * Set delaiEnvoi
     *
     * @param DelaiEnvoi $delaiEnvoi
     *
     * @return QuestionnaireType
     */
    public function setDelaiEnvoi(DelaiEnvoi $delaiEnvoi)
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
     * @return QuestionnaireType
     */
    public function setDelaiReception(DelaiReception $delaiReception)
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
     * Sets translatable locale
     *
     * @param string $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
