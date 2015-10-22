<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table(name="Ticket")
 * @ORM\Entity
 */
class Ticket
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
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\TicketType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=true)
     */
    private $type;


    /**
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=true)
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\TicketCategorie")
     * @ORM\JoinColumn(name="categorie", referencedColumnName="id")
     */
    private $categorie;


    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;


    private $acteur;
    public function getActeur(){return $this->acteur;}
    public function setActeur($acteur){$this->acteur = $acteur; return $this;}


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
     * Set type
     *
     * @param \SceauBundle\Entity\TicketType $type
     *
     * @return Ticket
     */
    public function setType(\SceauBundle\Entity\TicketType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \SceauBundle\Entity\TicketType
     */
    public function getType()
    {
        return $this->type;
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
    public function getCode()
    {
        return $this->code;
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
     * Set category
     * @param \SceauBundle\Entity\TicketCategorie $categorie
     *
     * @return Ticket
     */
    private function setCategorie(\SceauBundle\Entity\TicketCategorie $categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get category
     *
     * @return \SceauBundle\Entity\TicketCategorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }
}
