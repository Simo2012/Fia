<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProduitQuestionnaireReponse
 *
 * @ORM\Table(name="ProduitQuestionnaireReponse")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\ProduitQuestionnaireReponseRepository")
 */
class ProduitQuestionnaireReponse
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
     * @ORM\Column(name="note", type="decimal", precision=10, scale=2 )
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=500)
     */
    private $commentaire;


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
     * Set note
     *
     * @param string $note
     *
     * @return ProduitQuestionnaireReponse
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
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return ProduitQuestionnaireReponse
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
}
