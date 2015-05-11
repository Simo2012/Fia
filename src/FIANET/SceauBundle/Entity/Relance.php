<?php

namespace FIANET\SceauBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Relance
 *
 * @ORM\Table(name="Relance")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\RelanceRepository")
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
     * @ORM\Column(name="dateCreation", type="datetimetz")
     */
    private $dateCreation;

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
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Site", inversedBy="relances")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    /**
     * @var RelanceStatut
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\RelanceStatut")
     * @ORM\JoinColumn(name="relanceStatut_id", referencedColumnName="id", nullable=false)
     */
    private $statut;


    public function __construct()
    {
        $this->dateCreation = new DateTime();
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Relance
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
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
     * Set modele
     *
     * @param boolean $auto
     *
     * @return Relance
     */
    public function setModele($auto)
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
     * Set statut
     *
     * @param RelanceStatut $statut
     *
     * @return Relance
     */
    public function setStatut(RelanceStatut $statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return RelanceStatut
     */
    public function getStatut()
    {
        return $this->statut;
    }
}
