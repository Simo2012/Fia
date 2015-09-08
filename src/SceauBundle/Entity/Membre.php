<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Membre
 *
 * @ORM\Table(name="Membre")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\MembreRepository")
 */
class Membre
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
     * @ORM\Column(name="nom", type="string", length=50)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=50)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="pseudo", type="string", length=50, unique=true)
     */
    private $pseudo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetimetz")
     */
    private $dateCreation;


    /**
     * @var MembreStatut
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\MembreStatut")
     * @ORM\JoinColumn(name="membreStatut_id", referencedColumnName="id", nullable=false)
     */
    private $membreStatut;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SceauBundle\Entity\Email")
     * @ORM\JoinTable(name="Membre_Email",
     *      joinColumns={@ORM\JoinColumn(name="membre_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="email_id", referencedColumnName="id")}
     *      )
     */
    private $emails;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SceauBundle\Entity\Abonnement")
     * @ORM\JoinTable(name="Membre_Abonnement",
     *      joinColumns={@ORM\JoinColumn(name="membre_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="abonnement_id", referencedColumnName="id")}
     *      )
     */
    private $abonnements;

    /**
     * @var Avatar
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Avatar")
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id", nullable=true)
     */
    private $avatar;

    /**
     * @var Civilite
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Civilite")
     * @ORM\JoinColumn(name="civilite_id", referencedColumnName="id", nullable=false)
     */
    private $civilite;

    /**
     * @var Coordonnee
     *
     * @ORM\OneToOne(targetEntity="SceauBundle\Entity\Coordonnee")
     * @ORM\JoinColumn(name="coordonnee_id", referencedColumnName="id", nullable=false)
     */
    private $coordonnee;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\Questionnaire", mappedBy="membre")
     */
    private $questionnaires;


    public function __construct()
    {
        $this->abonnements = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->questionnaires = new ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Membre
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Membre
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set pseudo
     *
     * @param string $pseudo
     *
     * @return Membre
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Membre
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
     * Set membreStatut
     *
     * @param MembreStatut $membreStatut
     *
     * @return Membre
     */
    public function setMembreStatut(MembreStatut $membreStatut)
    {
        $this->membreStatut = $membreStatut;

        return $this;
    }

    /**
     * Get membreStatut
     *
     * @return MembreStatut
     */
    public function getMembreStatut()
    {
        return $this->membreStatut;
    }

    /**
     * Add abonnement
     *
     * @param Abonnement $abonnement
     *
     * @return Membre
     */
    public function addAbonnement(Abonnement $abonnement)
    {
        $this->abonnements[] = $abonnement;

        return $this;
    }

    /**
     * Remove abonnement
     *
     * @param Abonnement $abonnement
     */
    public function removeAbonnement(Abonnement $abonnement)
    {
        $this->abonnements->removeElement($abonnement);
    }

    /**
     * Get abonnements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAbonnements()
    {
        return $this->abonnements;
    }

    /**
     * Set avatar
     *
     * @param Avatar $avatar
     *
     * @return Membre
     */
    public function setAvatar(Avatar $avatar = null)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return Avatar
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Add email
     *
     * @param Email $email
     *
     * @return Membre
     */
    public function addEmail(Email $email)
    {
        $this->emails[] = $email;

        return $this;
    }

    /**
     * Remove email
     *
     * @param Email $email
     */
    public function removeEmail(Email $email)
    {
        $this->emails->removeElement($email);
    }

    /**
     * Get emails
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set civilite
     *
     * @param Civilite $civilite
     *
     * @return Membre
     */
    public function setCivilite(Civilite $civilite)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get civilite
     *
     * @return Civilite
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * Set coordonnee
     *
     * @param Coordonnee $coordonnee
     *
     * @return Membre
     */
    public function setCoordonnee(Coordonnee $coordonnee)
    {
        $this->coordonnee = $coordonnee;

        return $this;
    }

    /**
     * Get coordonnee
     *
     * @return Coordonnee
     */
    public function getCoordonnee()
    {
        return $this->coordonnee;
    }
}
