<?php

namespace FIANET\SceauBundle\Entity;

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
     * @ORM\Column(name="commentaire", type="string", length=500)
     */
    private $commentaire;

    /**
     * @var float
     *
     * @ORM\Column(name="note", type="float")
     */
    private $note;


    /**
     * @var Reponse
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Reponse")
     * @ORM\JoinColumn(name="reponse_id", referencedColumnName="id", nullable=false)
     **/
    private $reponse;


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
     * @param float $note
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
     * @return float
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
}
