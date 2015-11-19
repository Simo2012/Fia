<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TicketRepository")
 * @ORM\EntityListeners({"SceauBundle\Listener\Entity\TicketListener"})
 */
class Ticket
{
    const CATEGORIE_AVIS      = 1;
    const CATEGORIE_CONTACT   = 2;

    public static $CATEGORIES = [
        self::CATEGORIE_AVIS      => 'Avis',
        self::CATEGORIE_CONTACT   => 'Contact',
    ];

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
     * @ORM\Column(name="question", type="text")
     */
    private $question;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetimetz")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean")
     */
    private $etat;

    /**
     * @var integer
     *
     * @ORM\Embedded(class="TicketType", columnPrefix="type_")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="categorie", type="integer", nullable=true)
     */
    private $categorie;

    public function __construct()
    {
        $this->date     = new \DateTime();
        $this->type     = new TicketType();
        $this->etat     = false;
    }

    /**
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=true)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var auteur
     *
     * @ORM\OneToOne(targetEntity="SceauBundle\Entity\TicketAuteur", mappedBy="ticket", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="auteur_id",referencedColumnName="id")
     */
    private $auteur; 

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
     * Set question
     *
     * @param string $question
     *
     * @return Ticket
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Ticket
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
     * Set etat
     *
     * @param boolean $etat
     *
     * @return Ticket
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return boolean
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @return \SceauBundle\Entity\TicketType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \SceauBundle\Entity\TicketType $type
     * 
     * @return Ticket
     */
    public function setType(TicketType $type)
    {
        $this->type = $type;
        
        return $this;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Ticket
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
     * Set site
     * @param \SceauBundle\Entity\Site $site
     *
     * @return Ticket
     */
    public function setSite(\SceauBundle\Entity\Site $site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \SceauBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set auteur
     *
     * @param \SceauBundle\Entity\TicketAuteur $auteur
     *
     * @return Ticket
     */
    public function setAuteur(\SceauBundle\Entity\TicketAuteur $auteur = null)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \SceauBundle\Entity\TicketAuteur
     */
    public function getAuteur()
    {
        return $this->auteur;
    }


    /**
     * Set categorie
     *
     * @param integer $categorie
     *
     * @return Ticket
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return integer
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @return string
     */
    public function getCategorieLabel()
    {
        return isset(self::$CATEGORIES[$this->categorie]) ? self::$CATEGORIES[$this->categorie] : '';
    }

    public static function getAvailableCategories()
    {
        return self::$CATEGORIES;
    }
}
