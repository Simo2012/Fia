<?php

namespace FIANET\SceauBundle\Entity;

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
     * @ORM\Column(name="modele", type="boolean")
     */
    private $modele;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FIANET\SceauBundle\Entity\Questionnaire")
     * @ORM\JoinTable(name="Relance_Questionnaire",
     *      joinColumns={@ORM\JoinColumn(name="relance_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="questionnaire_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $questionnaires;

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
        $this->phonenumbers = new ArrayCollection();
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
     * @param boolean $modele
     *
     * @return Relance
     */
    public function setModele($modele)
    {
        $this->modele = $modele;

        return $this;
    }

    /**
     * Get modele
     *
     * @return boolean
     */
    public function getModele()
    {
        return $this->modele;
    }

    /**
     * Add questionnaire
     *
     * @param Questionnaire $questionnaire
     *
     * @return Relance
     */
    public function addQuestionnaire(Questionnaire $questionnaire)
    {
        $this->questionnaires[] = $questionnaire;

        return $this;
    }

    /**
     * Remove questionnaire
     *
     * @param Questionnaire $questionnaire
     */
    public function removeQuestionnaire(Questionnaire $questionnaire)
    {
        $this->questionnaires->removeElement($questionnaire);
    }

    /**
     * Get questionnaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionnaires()
    {
        return $this->questionnaires;
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
