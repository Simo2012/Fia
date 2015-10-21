<?php

namespace SceauBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Relance
 *
 * @ORM\Table(name="Relance")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\RelanceRepository")
 */
class Relance
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetimetz")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=500, nullable=true)
     */
    private $commentaire;

    /**
     * @var string
     *
     * @ORM\Column(name="objet", type="string", length=255, nullable=true)
     */
    private $objet;

    /**
     * @var string
     *
     * @ORM\Column(name="corps", type="text", nullable=true)
     */
    private $corps;

    /**
     * @var boolean
     *
     * @ORM\Column(name="auto", type="boolean")
     */
    private $auto;


    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Site", inversedBy="relances")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    /**
     * @var RelanceStatut
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\RelanceStatut")
     * @ORM\JoinColumn(name="relanceStatut_id", referencedColumnName="id", nullable=false)
     */
    private $relanceStatut;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Langue")
     * @ORM\JoinColumn(name="langue_id", referencedColumnName="id", nullable=false)
     */
    private $langue;

    /**
     * @var RelanceStatut
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\QuestionnaireType")
     * @ORM\JoinColumn(name="questionnaireType_id", referencedColumnName="id", nullable=false)
     */
    private $questionnaireType;


    public function __construct()
    {
        $this->date = new DateTime();
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Relance
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Relance
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
     * Set objet
     *
     * @param string $objet
     *
     * @return Relance
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * Set corps
     *
     * @param string $corps
     *
     * @return Relance
     */
    public function setCorps($corps)
    {
        $this->corps = $corps;

        return $this;
    }

    /**
     * Get corps
     *
     * @return string
     */
    public function getCorps()
    {
        return $this->corps;
    }

    /**
     * Set auto
     *
     * @param boolean $auto
     *
     * @return Relance
     */
    public function setAuto($auto)
    {
        $this->auto = $auto;

        return $this;
    }

    /**
     * Get auto
     *
     * @return boolean
     */
    public function getAuto()
    {
        return $this->auto;
    }

    /**
     * Set site
     *
     * @param Site $site
     *
     * @return Relance
     */
    public function setSite(Site $site)
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
     * Set relanceStatut
     *
     * @param RelanceStatut $relanceStatut
     *
     * @return Relance
     */
    public function setRelanceStatut(RelanceStatut $relanceStatut)
    {
        $this->relanceStatut = $relanceStatut;

        return $this;
    }

    /**
     * Get relanceStatut
     *
     * @return RelanceStatut
     */
    public function getRelanceStatut()
    {
        return $this->relanceStatut;
    }

    /**
     * Set langue
     *
     * @param Langue $langue
     *
     * @return Relance
     */
    public function setLangue(Langue $langue)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return Langue
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set questionnaireType
     *
     * @param QuestionnaireType $questionnaireType
     *
     * @return Relance
     */
    public function setQuestionnaireType(QuestionnaireType $questionnaireType)
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
