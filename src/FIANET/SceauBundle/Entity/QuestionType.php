<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionType
 *
 * @ORM\Table(name="QuestionType")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\QuestionTypeRepository")
 */
class QuestionType
{
    const CHOIX_UNIQUE = 1;
    const CHOIX_MULTIPLE = 2;
    const NOTATION = 3;
    const COMMENTAIRE = 4;
    const ETOILE = 5;

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
     */
    private $libelle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="personnalisable", type="boolean", options={"default" = true})
     */
    private $personnalisable;


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
     * @return QuestionType
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
     * Set personnalisable
     *
     * @param boolean $personnalisable
     *
     * @return QuestionType
     */
    public function setPersonnalisable($personnalisable)
    {
        $this->personnalisable = $personnalisable;

        return $this;
    }

    /**
     * Get personnalisable
     *
     * @return boolean
     */
    public function getPersonnalisable()
    {
        return $this->personnalisable;
    }
}
