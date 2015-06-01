<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionnaireReponse
 *
 * @ORM\Table(name="QuestionnaireReponse")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\QuestionnaireReponseRepository")
 */
class QuestionnaireReponse
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
     * @ORM\Column(name="commentaire", type="string", length=500, nullable=true)
     */
    private $commentaire;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $note;


    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Question")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false)
     **/
    private $question;

    /**
     * @var Reponse
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Reponse", inversedBy="questionnaireReponses")
     * @ORM\JoinColumn(name="reponse_id", referencedColumnName="id", nullable=false)
     **/
    private $reponse;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Questionnaire", inversedBy="questionnaireReponses")
     * @ORM\JoinColumn(name="questionnaire_id", referencedColumnName="id", nullable=false)
     **/
    private $questionnaire;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FIANET\SceauBundle\Entity\DroitDeReponse", mappedBy="questionnaireReponse")
     */
    private $droitDeReponses;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->droitDeReponses = new ArrayCollection();
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
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return QuestionnaireReponse
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return QuestionnaireReponse
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set reponse
     *
     * @param Reponse $reponse
     *
     * @return QuestionnaireReponse
     */
    public function setReponse(Reponse $reponse)
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
     * Add droitDeReponse
     *
     * @param DroitDeReponse $droitDeReponse
     *
     * @return QuestionnaireReponse
     */
    public function addDroitDeReponse(DroitDeReponse $droitDeReponse)
    {
        $this->droitDeReponses[] = $droitDeReponse;

        return $this;
    }

    /**
     * Remove droitDeReponse
     *
     * @param DroitDeReponse $droitDeReponse
     */
    public function removeDroitDeReponse(DroitDeReponse $droitDeReponse)
    {
        $this->droitDeReponses->removeElement($droitDeReponse);
    }

    /**
     * Get droitDeReponses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDroitDeReponses()
    {
        return $this->droitDeReponses;
    }

    /**
     * Set questionnaire
     *
     * @param Questionnaire $questionnaire
     *
     * @return QuestionnaireReponse
     */
    public function setQuestionnaire(Questionnaire $questionnaire)
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }

    /**
     * Get questionnaire
     *
     * @return Questionnaire
     */
    public function getQuestionnaire()
    {
        return $this->questionnaire;
    }

    /**
     * Set question
     *
     * @param Question $question
     *
     * @return QuestionnaireReponse
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
}
