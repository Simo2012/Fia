<?php

namespace SceauBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Membre
 *
 * @ORM\Table(name="Membre")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\MembreRepository")
 */
class Membre implements AdvancedUserInterface, \Serializable
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
    * @var ArrayCollection
    * 
    * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\TrancheAge")
    * @ORM\JoinColumn(name="trancheage_id", referencedColumnName="id", nullable=true)
    */
    private $trancheAge;

    /**
     * @var Avatar
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Avatar")
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id", nullable=true)
     */
    private $avatar;

    /**
    * @var string
    *
    * @ORM\Column(name="civilite", type="string", length=10)
    */
    private $civilite;

    /**
     * @var Coordonnee
     *
     * @ORM\OneToOne(targetEntity="SceauBundle\Entity\Coordonnee")
     * @ORM\JoinColumn(name="coordonnee_id", referencedColumnName="id", nullable=true)
     */
    private $coordonnee;
    
    /**
    * @var string
    *
    * @ORM\Column(name="password", type="string", length=100, nullable=false)
    */
    private $password;
    
    /**
    * @var boolean
    *
    * @ORM\Column(name="active", type="boolean", nullable=false)
    */
    private $active;
    
    /**
    * @var boolean
    *
    * @ORM\Column(name="newsletter", type="boolean", nullable=true)
    */
    private $newsletter;
    
    

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
        $this->active = true;
        $this->newsletter = false;
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
     * @param String $civilite
     *
     * @return Membre
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get String
     *
     * @return Civilite
     */
    public function getCivilite()
    {
        return $this->civilite;
    }
    
    /**
     * Get Bool
     *
     * @return newsletter
     */
    public function getNewsletter()
    {
        return $this->newsletter;
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
    
    /**
     * Get TrancheAge
     * 
     * @return TrancheAge
     */
    function getTrancheAge() {
        return $this->trancheAge;
    }
    
    /**
     * @param TrancheAge $trancheAge
     *
     * @return Membre
     */
    function setTrancheAge(TrancheAge $trancheAge) {
        $this->trancheAge = $trancheAge;
    }

    
    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
        
    }
    
    /**
    * Set password
    *
    * @param string $password
    * @return Membre
    */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    
    /**
    * Get password
    *
    * @return string
    */
   public function getPassword() {
        return $this->password;
    }
    
    /**
     * 
     * @return type
     */
    public function getRoles() {
        return array('ROLE_USER');
    }

     /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return '';
    }

    public function getUsername() {
        return $this->emails;
        
    }

    public function isAccountNonExpired() {
        return true;
    }

     /**
     * @inheritDoc
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled() {
        return $this->active;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            ) = unserialize($serialized);
    }
    
    /**
    * Set active
    *
    * @param boolean $active
    * @return Membre
    */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }
    
    /**
    * Set newsletter
    *
    * @param boolean $newsletter
    * @return Membre
    */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

}
