<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketReponse
 *
 * @ORM\Table(name="TicketReponse")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TicketReponseRepository")
 * @ORM\EntityListeners({"SceauBundle\Listener\Entity\TicketReponseListener"})
 */
class TicketReponse
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
     * @var Ticket
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Ticket", inversedBy="reponses")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id", nullable=true)
     */
    private $ticket;

    /**
     * @var TicketReponseModele
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\TicketReponseModele")
     * @ORM\JoinColumn(name="ticket_reponse_modele_id", referencedColumnName="id", nullable=true)
     */
    private $reponseModele;

    /**
     * @var string
     *
     * @ORM\Column(name="mailFrom", type="string", length=255)
     */
    private $mailFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="mailTo", type="string", length=255)
     */
    private $mailTo;

    /**
     * @var string
     *
     * @ORM\Column(name="mailSubject", type="string", length=255)
     */
    private $mailSubject;

    /**
     * @var string
     *
     * @ORM\Column(name="mailBody", type="text", nullable=true)
     */
    private $mailBody;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetimetz")
     */
    private $date;
    
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
     * Set mailFrom
     *
     * @param string $mailFrom
     *
     * @return TicketReponse
     */
    public function setMailFrom($mailFrom)
    {
        $this->mailFrom = $mailFrom;

        return $this;
    }

    /**
     * Get mailFrom
     *
     * @return string
     */
    public function getMailFrom()
    {
        return $this->mailFrom;
    }

    /**
     * Set mailTo
     *
     * @param string $mailTo
     *
     * @return TicketReponse
     */
    public function setMailTo($mailTo)
    {
        $this->mailTo = $mailTo;

        return $this;
    }

    /**
     * Get mailTo
     *
     * @return string
     */
    public function getMailTo()
    {
        return $this->mailTo;
    }

    /**
     * Set mailSubject
     *
     * @param string $mailSubject
     *
     * @return TicketReponse
     */
    public function setMailSubject($mailSubject)
    {
        $this->mailSubject = $mailSubject;

        return $this;
    }

    /**
     * Get mailSubject
     *
     * @return string
     */
    public function getMailSubject()
    {
        return $this->mailSubject;
    }

    /**
     * Set mailBody
     *
     * @param string $mailBody
     *
     * @return TicketReponse
     */
    public function setMailBody($mailBody)
    {
        $this->mailBody = $mailBody;

        return $this;
    }

    /**
     * Get mailBody
     *
     * @return string
     */
    public function getMailBody()
    {
        return $this->mailBody;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TicketReponse
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
     * Set reponseModele
     *
     * @param \SceauBundle\Entity\TicketReponseModele $reponseModele
     *
     * @return TicketReponse
     */
    public function setReponseModele(\SceauBundle\Entity\TicketReponseModele $reponseModele = null)
    {
        $this->reponseModele = $reponseModele;

        return $this;
    }

    /**
     * Get reponseModele
     *
     * @return \SceauBundle\Entity\TicketReponseModele
     */
    public function getReponseModele()
    {
        return $this->reponseModele;
    }

    /**
     * Set ticket
     *
     * @param \SceauBundle\Entity\Ticket $ticket
     *
     * @return TicketReponse
     */
    public function setTicket(\SceauBundle\Entity\Ticket $ticket = null)
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
}
