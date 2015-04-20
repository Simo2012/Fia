<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

/**
 * DroitDeReponse
 *
 * @ORM\Table(name="DroitDeReponse")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\DroitDeReponseRepository")
 */
class DroitDeReponse implements GroupSequenceProviderInterface
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
     * @ORM\Column(name="commentaire", type="string", length=700)
     */
    private $commentaire;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateInsertion", type="datetimetz")
     */
    private $dateInsertion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateSuppression", type="datetimetz", nullable=true)
     */
    private $dateSuppression;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;
    
    
    /**
     * @var QuestionnaireReponse
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\QuestionnaireReponse", inversedBy="droitDeReponses")
     * @ORM\JoinColumn(name="questionnaireReponse_id", referencedColumnName="id", nullable=false)
     */
    private $questionnaireReponse;    

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateInsertion = new \DateTime('now');
        $this->actif = true;
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
     * @return DroitDeReponse
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
     * Set dateInsertion
     *
     * @param \DateTime $dateInsertion
     *
     * @return DroitDeReponse
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
     * Set dateSuppression
     *
     * @param \DateTime $dateSuppression
     *
     * @return DroitDeReponse
     */
    public function setDateSuppression($dateSuppression)
    {
        $this->dateSuppression = $dateSuppression;

        return $this;
    }

    /**
     * Get dateSuppression
     *
     * @return \DateTime
     */
    public function getDateSuppression()
    {
        return $this->dateSuppression;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return DroitDeReponse
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
     * Set questionnaireReponse
     *
     * @param \FIANET\SceauBundle\Entity\QuestionnaireReponse $questionnaireReponse
     *
     * @return DroitDeReponse
     */
    public function setQuestionnaireReponse(\FIANET\SceauBundle\Entity\QuestionnaireReponse $questionnaireReponse)
    {
        $this->questionnaireReponse = $questionnaireReponse;

        return $this;
    }

    /**
     * Get questionnaireReponse
     *
     * @return \FIANET\SceauBundle\Entity\QuestionnaireReponse
     */
    public function getQuestionnaireReponse()
    {
        return $this->questionnaireReponse;
    }

    /**
     * Retourne la séquence des groupes de validations à respecter en fonction du droit de réponse saisi :
     * Vérification du contenu à la validation.
     *
     * @return array Tableau ordonné avec les groupes de validation
     */
    public function getGroupSequence()
    {
        $groupSequence = array();

        $groupSequence[] = 'validation';

        return $groupSequence;
    }

}
