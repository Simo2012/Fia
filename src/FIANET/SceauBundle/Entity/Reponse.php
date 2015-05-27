<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reponse
 *
 * @ORM\Table(name="Reponse")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\ReponseRepository")
 */
class Reponse
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
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;


    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Question", inversedBy="reponses")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false)
     */
    private $question;


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
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Reponse
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
}
