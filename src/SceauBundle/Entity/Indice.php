<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * NoteType
 *
 * @ORM\Table(name="Indice")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\IndiceRepository")
 */
class Indice
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
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;


    /**
     * @var IndiceType
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\IndiceType")
     * @ORM\JoinColumn(name="indiceType_id", referencedColumnName="id", nullable=false)
     */
    private $indiceType;

    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Question")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=true)
     */
    private $question;

    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Reponse")
     * @ORM\JoinColumn(name="reponse_id", referencedColumnName="id", nullable=true)
     */
    private $reponse;

    /**
     * @var Indice
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Indice", inversedBy="indicesSecondaires")
     * @ORM\JoinColumn(name="indice_id", referencedColumnName="id", nullable=true)
     */
    private $indicePrimaire;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Indice", mappedBy="indicePrimaire")
     */
    private $indicesSecondaires;

    /**
     * @var QuestionnaireType
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\QuestionnaireType", inversedBy="indices")
     * @ORM\JoinColumn(name="questionnaireType_id", referencedColumnName="id", nullable=false)
     */
    private $questionnaireType;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->indicesSecondaires = new ArrayCollection();
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
     *
     * @return Indice
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
     * Set indiceType
     *
     * @param IndiceType $indiceType
     *
     * @return Indice
     */
    public function setIndiceType(IndiceType $indiceType)
    {
        $this->indiceType = $indiceType;

        return $this;
    }

    /**
     * Get indiceType
     *
     * @return IndiceType
     */
    public function getIndiceType()
    {
        return $this->indiceType;
    }

    /**
     * Set question
     *
     * @param Question $question
     *
     * @return Indice
     */
    public function setQuestion(Question $question = null)
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
     * Set reponse
     *
     * @param Reponse $reponse
     *
     * @return Indice
     */
    public function setReponse(Reponse $reponse = null)
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * Get reponse
     *
     * @return Reponse
     */
    public function getReponse()
    {
        return $this->reponse;
    }

    /**
     * Set indicePrimaire
     *
     * @param Indice $indicePrimaire
     *
     * @return Indice
     */
    public function setIndicePrimaire(Indice $indicePrimaire = null)
    {
        $this->indicePrimaire = $indicePrimaire;

        return $this;
    }

    /**
     * Get indicePrimaire
     *
     * @return Indice
     */
    public function getIndicePrimaire()
    {
        return $this->indicePrimaire;
    }

    /**
     * Add indicesSecondaire
     *
     * @param Indice $indiceSecondaire
     *
     * @return Indice
     */
    public function addIndiceSecondaire(Indice $indiceSecondaire)
    {
        $this->indicesSecondaires[] = $indiceSecondaire;

        return $this;
    }

    /**
     * Remove indicesSecondaire
     *
     * @param Indice $indiceSecondaire
     */
    public function removeIndiceSecondaire(Indice $indiceSecondaire)
    {
        $this->indicesSecondaires->removeElement($indiceSecondaire);
    }

    /**
     * Get indicesSecondaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIndicesSecondaires()
    {
        return $this->indicesSecondaires;
    }

    /**
     * Set questionnaireType
     *
     * @param QuestionnaireType $questionnaireType
     *
     * @return Indice
     */
    public function setQuestionnaireType(QuestionnaireType $questionnaireType = null)
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
