<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketHistorique
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TicketHistoriqueRepository")
 */
class TicketHistorique
{
    // constantes historiqueee
    const TICKET_CREATION                   = 1;
    const TICKET_REPONSE                    = 2;
    const TICKET_STATE_CHANGE               = 3;
    const TICKET_NOTE_CREATE                = 4; 
    const TICKET_NOTE_UPDATE                = 5; 
    const TICKET_NOTE_DELETE                = 6; 
    const TICKET_REAFECTATION_CATEGORIE     = 7;
    const TICKET_REAFECTATION_MODERATEUR    = 8;

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
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Ticket")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     */
    private $ticket;

    /**
     * @ORM\OneToOne(targetEntity="SceauBundle\Entity\TicketHistoriqueEmail")
     */
    private $ticketHistoriqueEmail;

    /**
     * @var integer
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user;   //TODO remove fix

    public function __construct()
    {
        $this->date = new \DateTime();
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
     * @return TicketHistorique
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
     * Set comment
     *
     * @param string $comment
     *
     * @return TicketHistorique
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return TicketHistorique
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set ticket
     *
     * @param \SceauBundle\Entity\Ticket $ticket
     *
     * @return TicketHistorique
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return \SceauBundle\Entity\Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return TicketHistorique
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set ticketHistoriqueEmail
     *
     * @param \SceauBundle\Entity\TicketHistoriqueEmail $ticketHistoriqueEmail
     *
     * @return TicketHistorique
     */
    public function setTicketHistoriqueEmail(\SceauBundle\Entity\TicketHistoriqueEmail $ticketHistoriqueEmail = null)
    {
        $this->ticketHistoriqueEmail = $ticketHistoriqueEmail;

        return $this;
    }

    /**
     * Get ticketHistoriqueEmail
     *
     * @return \SceauBundle\Entity\TicketHistoriqueEmail
     */
    public function getTicketHistoriqueEmail()
    {
        return $this->ticketHistoriqueEmail;
    }
}
